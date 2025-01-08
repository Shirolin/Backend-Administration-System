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
        $grid = new Grid(new Teacher(['adminUser']));

        $grid->column('id', __('ID'))->sortable();
        $grid->column('nickname', __('Nickname'));
        $grid->column('adminUser.username', __('Username'));
        $grid->column('adminUser.name', __('Name'));
        $grid->column('adminUser.avatar', __('Avatar'))->image('', 50, 50);
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
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Teacher());

        $form->display('id', __('ID'));
        $form->text('nickname', __('Nickname'));
        $form->display('created_at', __('Created At'));
        $form->display('updated_at', __('Updated At'));

        return $form;
    }
}
