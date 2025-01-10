<?php

namespace App\Admin\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Services\TeacherService;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;

class TeacherController extends AdminController
{
    protected $title = '教师管理';
    protected $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

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
            // 因为要同时创建/更新多个表的数据
            // 所以这里拦截保存操作，将表单数据传递给服务类处理
            try {
                if ($form->isCreating()) {
                    $this->teacherService->createTeacher($form);
                } else {
                    $this->teacherService->updateTeacher($form);
                }

                $success = new MessageBag([
                    'title'   => '操作成功',
                    'message' => $form->isCreating() ? '创建教师成功' : '编辑教师成功',
                ]);

                return redirect(admin_url('teachers'))->with(compact('success'));
            } catch (\Exception $e) {
                admin_error($form->isCreating() ? '创建教师失败' : '编辑教师失败', $e->getMessage());
                return back()->withInput();
            }
        });

        $form->footer(function ($footer) {
            $footer->disableReset();
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
        });

        return $form;
    }
}
