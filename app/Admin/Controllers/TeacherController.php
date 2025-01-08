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
        $grid->column('nickname', __('Nickname'));
        $grid->column('adminUser.username', __('Username'));
        $grid->column('adminUser.name', '管理员名');
        $grid->column('adminUser.avatar', __('Avatar'))->image('', 50, 50);
        $grid->column('user.name', '用户名');
        $grid->column('user.email', __('Email'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Teacher::findOrFail($id)->load('adminUser'));

        $show->field('id', __('ID'));
        $show->field('nickname', __('Nickname'));
        $show->field('adminUser.username', __('Username'));
        $show->field('adminUser.name', __('Name'));
        $show->field('adminUser.avatar', __('Avatar'))->image('', 50, 50);
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
    protected function oldform()
    {
        $form = new Form(new Teacher(['adminUser']));

        // 创建时的表单
        // "username"(必填) -> "admin_users"."username" -> "users"."username"
        // "nickname"(必填) -> "admin_users"."name" -> "users"."nickname" -> "teachers"."nickname"
        // "email"(必填) -> "users"."email"
        // "password"(必填) -> "admin_users"."password" -> "users"."password"
        // "role"(固定教师值) -> "users"."role"
        // "avatar"(选填) -> "admin_users"."avatar" -> "users"."avatar"
        // "created_at"(自动) -> "admin_users"."created_at" -> "users"."created_at" -> "teachers"."created_at"
        // "user_id"(自动) -> "users"."id" -> "teachers"."id"
        // "id"(自动) -> "admin_users"."id" -> "teachers"."admin_id"
        if ($form->isCreating()) {
            $form->text('adminUser.username', __('Username'))->rules('required');
            $form->text('adminUser.name', __('Name'))->rules('required');
            $form->email('user.email', __('Email'))->rules('required');
            $form->password('adminUser.password', __('Password'))->rules('required');
            $form->hidden('user.role')->value(\App\Models\User::ROLE_TEACHER);
            $form->image('adminUser.avatar', __('Avatar'));
        }

        return $form;
    }

    protected function form()
    {
        $form = new Form(new Teacher());

        $form->text('username', __('用户名'))->rules('required|unique:users,username|unique:admin_users,username');
        $form->text('nickname', __('昵称'))->rules('required|unique:users,nickname|unique:teachers,nickname');
        $form->email('email', __('邮箱'))->rules('required|email|unique:users,email');
        $form->password('password', __('密码'))->rules('required|min:6')->default(str_random(10));
        $form->password('password_confirmation', __('确认密码'))->rules('same:password');
        $form->image('avatar', __('头像'));

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

        return $form;
    }
}
