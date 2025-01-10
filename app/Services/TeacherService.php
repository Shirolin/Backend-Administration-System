<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\User;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Form;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Log;

class TeacherService
{
    /**
     * 创建教师
     *  1. 创建管理员
     *  2. 赋予管理员教师角色
     *  3. 创建用户
     *  4. 创建教师
     * 
     * @param Form $form
     * @throws \Exception
     */
    public function createTeacher(Form $form): void
    {
        DB::beginTransaction();
        try {
            // 创建 admin_users 记录
            $adminUser = Administrator::create([
                'username' => $form->username,
                'password' => Hash::make($form->password),
                'name' => $form->nickname,
            ]);

            // 设置管理员为教师角色
            $role = Role::where('slug', 'teacher')->first();
            if ($adminUser && $role) {
                $adminUser->roles()->attach($role->id);
            }

            // 创建 users 记录
            $user = User::create([
                'username' => $form->username,
                'nickname' => $form->nickname,
                'email' => $form->email,
                'role' => User::ROLE_TEACHER,
                'password' => Hash::make($form->password),
            ]);

            // 创建 teachers 记录
            $teacher = new Teacher();
            $teacher->id = $user->id;
            $teacher->nickname = $form->nickname;
            $teacher->admin_id = $adminUser->id;
            $teacher->save();

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 更新教师
     *  1. 更新管理员记录
     *  2. 更新用户记录
     *  3. 更新教师记录
     * 
     * @param Form $form
     * @throws \Exception
     */
    public function updateTeacher(Form $form): void
    {
        DB::beginTransaction();
        try {
            $teacher = $form->model();
            $user = $teacher->user;
            $adminUser = $teacher->adminUser;

            // 检查昵称是否已存在管理员表中
            $userNameExists = Administrator::where('id', '<>', $teacher->admin_id)->where('name', $form->nickname)->exists();
            if ($userNameExists) {
                throw new \Exception('昵称已存在');
            }

            // 更新 admin_users 记录
            $adminUser->username = $form->username;
            if ($form->password && $adminUser->password != $form->password) {
                $adminUser->password = Hash::make($form->password);
            }
            $adminUser->name = $form->nickname;
            $adminUser->save();

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

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }
}
