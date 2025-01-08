<?php

namespace App\Admin\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;

class TeacherController extends AdminController
{
    protected $title = '教师管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Teacher(['adminUser', 'User']));

        $grid->column('id', __('ID'))->sortable();
        $grid->column('adminUser.username', __('Username'));
        $grid->column('nickname', __('Nickname'));
        $grid->column('adminUser.avatar', __('Avatar'))->image('', 50, 50);
        $grid->column('user.email', __('Email'));
        $grid->column('user.role', __('Role'))->using(User::getRoleMap());
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Teacher::findOrFail($id)->load(['adminUser', 'User']));

        $show->field('id', __('ID'));
        $show->field('adminUser.username', __('Username'));
        $show->field('nickname', __('Nickname'));
        $show->field('adminUser.avatar', __('Avatar'))->image('', 50, 50);
        $show->field('user.email', __('Email'));
        $show->field('user.role', __('Role'))->using(User::getRoleMap());
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     * 创建教师时会先在用户表中创建教师用户，然后在管理员表中创建用户，最后在教师表中创建教师，教师ID与用户ID关联、管理员ID与教师的admin_id关联
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Teacher());

        $form->text('username', __('用户名'))->rules('required|unique:users,username|unique:admin_users,username|regex:/^[a-zA-Z0-9_]+$/', [
            'required' => '用户名不能为空',
            'unique'   => '用户名已存在',
            'regex'    => '用户名只能包含字母、数字和下划线',
        ]);
        $form->text('nickname', __('昵称'))->rules('required|unique:users,nickname|unique:teachers,nickname', [
            'required' => '昵称不能为空',
            'unique'   => '昵称已存在',
        ]);
        $form->email('email', __('邮箱'))->rules('required|email|unique:users,email', [
            'required' => '邮箱不能为空',
            'email'    => '邮箱格式不正确',
            'unique'   => '邮箱已存在',
        ]);
        $form->password('password', __('密码'))->rules('required|min:6', [
            'required' => '密码不能为空',
            'min'      => '密码至少为6位',
        ]);
        $form->password('password_confirmation', __('确认密码'))->rules('same:password', [
            'same' => '两次输入的密码不一致',
        ]);
        $form->image('avatar', __('头像'))->uniqueName()->rules('image', [
            'image' => '头像必须是图片',
        ]);

        $form->saving(function (Form $form) {
            DB::beginTransaction();
            try {
                // 1. 创建 admin_users 记录
                $adminUser = Administrator::create([
                    'username' => $form->username,
                    'password' => Hash::make($form->password),
                    'name' => $form->nickname, // admin_users 的 name 字段使用 nickname
                    'avatar' => $form->avatar,
                ]);

                // 2. 创建 users 记录
                $user = User::create([
                    'username' => $form->username,
                    'nickname' => $form->nickname,
                    'email' => $form->email,
                    'role' => User::ROLE_TEACHER, // 固定教师角色值
                    'password' => Hash::make($form->password),
                    'avatar' => $form->avatar,
                ]);

                // 3. 创建 teachers 记录
                $teacher = new Teacher(); // 使用 new Teacher() 而不是 $form->model()
                $teacher->id = $user->id; // 设置主键
                $teacher->nickname = $form->nickname;
                $teacher->admin_id = $adminUser->id;
                $teacher->save();

                DB::commit();
                $success = new MessageBag([
                    'title'   => '操作成功',
                    'message' => '创建教师成功',
                ]);

                // 保存成功后跳转到列表页
                return redirect(admin_url('teachers'))->with(compact('success'));
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error($e);
                admin_error('创建教师失败', $e->getMessage());
                return back()->withInput();
            }
        });

        $form->deleted(function (Form $form) {
            $teacher = $form->model();
            $user = $teacher->user;
            $adminUser = Administrator::find($teacher->admin_id);
            DB::beginTransaction();
            try {
                $teacher->delete();
                $user->delete();
                $adminUser->delete();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error($e);
                admin_error('删除教师失败', $e->getMessage());
                return back();
            }
        });

        $form->footer(function ($footer) {

            // 去掉`重置`按钮
            $footer->disableReset();

            // 去掉`提交`按钮
            // $footer->disableSubmit();

            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();
        });

        return $form;
    }
}
