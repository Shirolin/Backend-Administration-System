<?php

namespace App\Admin\Controllers;

use App\Models\Student;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;

class StudentController extends AdminController
{
    protected $title = '学生管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Student(['user']));

        $grid->column('id', __('ID'))->sortable();
        $grid->column('user.username', __('Username'));
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
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Student::findOrFail($id)->load('user'));

        $show->field('id', __('ID'));
        $show->field('user.username', __('Name'));
        $show->field('nickname', __('Nickname'));
        $show->field('user.email', __('Email'));
        $show->field('user.role', __('Role'))->using(User::getRoleMap());
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Student(['user']));

        $form->text('username', __('Username'))
            ->creationRules('required|unique:users,username|regex:/^[a-zA-Z0-9_]+$/', [
                'required' => '用户名不能为空',
                'unique'   => '用户名已存在',
                'regex'    => '用户名只能包含字母、数字和下划线',
            ])->updateRules('required|unique:users,username,{{id}}|regex:/^[a-zA-Z0-9_]+$/', [
                'required' => '用户名不能为空',
                'unique'   => '用户名已存在',
                'regex'    => '用户名只能包含字母、数字和下划线',
            ])->default(function ($form) {
                return optional($form->model()->user)->username;
            })->required();

        $form->text('nickname', __('Nickname'))
            ->creationRules('required|unique:users,nickname|unique:students,nickname', [
                'required' => '昵称不能为空',
                'unique'   => '昵称已存在',
            ])->updateRules('required|unique:users,nickname,{{id}}|unique:students,nickname,{{id}}', [
                'required' => '昵称不能为空',
                'unique'   => '昵称已存在',
            ])->default(function ($form) {
                return optional($form->model()->user)->nickname;
            })->required();

        $form->email('email', __('Email'))
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

        $form->password('password', __('Password'))
            ->creationRules('required|min:6', [
                'required' => '密码不能为空',
                'min'      => '密码至少为6位',
            ])->updateRules('nullable|min:6', [
                'min' => '密码至少为6位',
            ])->default(function ($form) {
                return optional($form->model()->user)->password;
            })->required();

        $form->password('password_confirmation', __('Password confirmation'))
            ->creationRules('required|same:password', [
                'required' => '确认密码不能为空',
                'same'     => '两次输入的密码不一致',
            ])->updateRules('nullable|same:password', [
                'same' => '两次输入的密码不一致',
            ])->default(function ($form) {
                return optional($form->model()->user)->password;
            })->required();

        $form->saving(function (Form $form) {
            DB::beginTransaction();
            try {
                if ($form->isCreating()) {
                    // 创建逻辑
                    // 1. 创建 users 记录
                    $user = User::create([
                        'username' => $form->username,
                        'nickname' => $form->nickname,
                        'email' => $form->email,
                        'role' => User::ROLE_STUDENT, // 固定学生角色值
                        'password' => Hash::make($form->password),
                    ]);

                    // 2. 创建 students 记录
                    $student = new Student(); // 使用 new Student() 而不是 $form->model()
                    $student->id = $user->id; // 设置主键
                    $student->nickname = $form->nickname;
                    $student->save();
                } else {
                    // 编辑逻辑
                    $student = $form->model();
                    $user = $student->user;

                    // 更新 users 记录
                    $user->username = $form->username;
                    $user->nickname = $form->nickname;
                    $user->email = $form->email;
                    if ($form->password && $user->password != $form->password) {
                        $user->password = Hash::make($form->password);
                    }
                    $user->save();

                    // 更新 students 记录
                    $student->nickname = $form->nickname;
                    $student->save();
                }

                DB::commit();
                $success = new MessageBag([
                    'title'   => '操作成功',
                    'message' => $form->isCreating() ? '创建学生成功' : '编辑学生成功',
                ]);

                // 保存成功后跳转到列表页
                return redirect(admin_url('students'))->with(compact('success'));
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error($e);
                admin_error($form->isCreating() ? '创建学生失败' : '编辑学生失败', $e->getMessage());
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
