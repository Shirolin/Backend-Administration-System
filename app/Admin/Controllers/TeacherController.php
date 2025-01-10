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
        $grid = new Grid(new Teacher(['adminUser', 'user']));

        $grid->column('id', __('ID'))->sortable();
        $grid->column('adminUser.username', __('Username'));
        $grid->column('nickname', __('Nickname'));
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
        $show = new Show(Teacher::findOrFail($id)->load(['adminUser', 'user']));

        $show->field('id', __('ID'));
        $show->field('adminUser.username', __('Username'));
        $show->field('nickname', __('Nickname'));
        $show->field('user.email', __('Email'));
        $show->field('user.role', __('Role'))->using(User::getRoleMap());
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     * 创建教师时会先在用户表中创建教师用户，
     * 然后在管理员表中创建用户，
     * 最后再教师表中创建教师，
     * 
     * 教师ID与用户ID关联、管理员ID与教师的admin_id关联
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Teacher(['adminUser', 'user']));

        $form->text('username', __('用户名'))
            ->creationRules('required|unique:users,username|unique:admin_users,username|regex:/^[a-zA-Z0-9_]+$/', [
                'required' => '用户名不能为空',
                'unique'   => '用户名已存在',
                'regex'    => '用户名只能包含字母、数字和下划线',
            ])->updateRules('required|unique:users,username,{{id}}|regex:/^[a-zA-Z0-9_]+$/', [
                'required' => '用户名不能为空',
                'unique'   => '用户名已存在',
                'regex'    => '用户名只能包含字母、数字和下划线',
            ])->default(function ($form) {
                return optional($form->model()->adminUser)->username;
            })->required();

        $form->text('nickname', __('昵称'))
            ->creationRules('required|unique:users,nickname|unique:teachers,nickname', [
                'required' => '昵称不能为空',
                'unique'   => '昵称已存在',
            ])->updateRules('required|unique:users,nickname,{{id}}|unique:teachers,nickname,{{id}}', [
                'required' => '昵称不能为空',
                'unique'   => '昵称已存在',
            ])->default(function ($form) {
                return optional($form->model()->user)->nickname;
            })->required();

        $form->email('email', __('邮箱'))
            ->creationRules('required|email|unique:users,email', [
                'required' => '邮箱不能为空',
                'email'    => '邮箱格式不正确',
                'unique'   => '邮箱已存在',
            ])->updateRules('required|email|unique:users,email,{{id}}', [
                'required' => '邮箱不能为空',
                'email'    => '邮箱格式不正确',
                'unique'   => '邮箱已存在',
            ])->default(function ($form) {
                return optional($form->model()->user)->email;
            })->required();

        $form->password('password', __('密码'))
            ->creationRules('required|min:6', [
                'required' => '密码不能为空',
                'min'      => '密码至少为6位',
            ])->updateRules('nullable|min:6', [
                'min' => '密码至少为6位',
            ])->default(function ($form) {
                return optional($form->model()->adminUser)->password;
            })->required();

        $form->password('password_confirmation', __('确认密码'))
            ->creationRules('required|same:password', [
                'required' => '确认密码不能为空',
                'same'     => '两次输入的密码不一致',
            ])->updateRules('nullable|same:password', [
                'same' => '两次输入的密码不一致',
            ])->default(function ($form) {
                return optional($form->model()->adminUser)->password;
            })->required();

        $form->saving(function (Form $form) {
            DB::beginTransaction();
            try {
                if ($form->isCreating()) {
                    // 创建逻辑
                    // 1. 创建 admin_users 记录
                    $adminUser = Administrator::create([
                        'username' => $form->username,
                        'password' => Hash::make($form->password),
                        'name' => $form->nickname, // admin_users 的 name 字段使用 nickname
                    ]);

                    // 2. 创建 users 记录
                    $user = User::create([
                        'username' => $form->username,
                        'nickname' => $form->nickname,
                        'email' => $form->email,
                        'role' => User::ROLE_TEACHER, // 固定教师角色值
                        'password' => Hash::make($form->password),
                    ]);

                    // 3. 创建 teachers 记录
                    $teacher = new Teacher(); // 使用 new Teacher() 而不是 $form->model()
                    $teacher->id = $user->id; // 设置主键
                    $teacher->nickname = $form->nickname;
                    $teacher->admin_id = $adminUser->id;
                    $teacher->save();
                } else {
                    // 编辑逻辑
                    $teacher = $form->model();
                    $user = $teacher->user;
                    $adminUser = $teacher->adminUser;

                    // 检查昵称是否已存在管理员表中
                    $userNameExists = Administrator::where('id', '<>', $teacher->admin_id)->where('name', $form->nickname)->exists();
                    if ($userNameExists) {
                        return back()->withInput()->withErrors(['nickname' => '昵称已存在']);
                    }

                    // 更新 admin_users 记录
                    $adminUser->username = $form->username;
                    if ($form->password && $adminUser->password != $form->password) {
                        $adminUser->password = Hash::make($form->password);
                    }
                    $adminUser->name = $form->nickname;
                    $adminUser->save();

                    // 检查用户名是否已存在用户表中

                    // 更新 users 记录
                    $user->username = $form->username;
                    $user->nickname = $form->nickname;
                    $user->email = $form->email;
                    if ($form->password && $user->password != $form->password) {
                        $user->password = Hash::make($form->password);
                    }
                    $user->save();

                    // 更新 teachers 记录
                    $teacher->nickname = $form->nickname;
                    $teacher->save();
                }

                DB::commit();
                $success = new MessageBag([
                    'title'   => '操作成功',
                    'message' => $form->isCreating() ? '创建教师成功' : '编辑教师成功',
                ]);

                // 保存成功后跳转到列表页
                return redirect(admin_url('teachers'))->with(compact('success'));
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error($e);
                admin_error($form->isCreating() ? '创建教师失败' : '编辑教师失败', $e->getMessage());
                return back()->withInput();
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
