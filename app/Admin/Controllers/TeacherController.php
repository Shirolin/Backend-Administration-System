<?php

namespace App\Admin\Controllers;

use App\Models\Teacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

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
     * (创建教师时会先在管理员表中创建用户，然后在用户表中创建教师用户，最后在教师表中创建教师，管理员ID、教师ID与用户ID关联)
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Teacher(['adminUser']));

        $form->display('id', __('ID'));
        if ($form->isCreating()) {
            $form->text('adminUser.username', __('Username'))->rules('required');
        } else {
            $form->display('adminUser.username', __('Username'));
        }

        $form->text('nickname', __('Nickname'));
        $form->text('adminUser.name', __('Name'));  
        $form->image('adminUser.avatar', __('Avatar'));

        if ($form->isCreating()) {
            $form->text('user.name', '用户名')->rules('required');
            $form->email('user.email', __('Email'))->rules('required');
        } else {
            $form->display('user.name', '用户名');
            $form->display('user.email', __('Email'));
        }
        
        $form->password('password', __('密码'))->rules('required|min:6');
        $form->password('password_confirmation', __('确认密码'))->rules('same:password');

        // $form->display('created_at', __('Created At'));
        // $form->display('updated_at', __('Updated At'));

        return $form;
    }
}
