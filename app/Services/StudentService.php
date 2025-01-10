<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Encore\Admin\Form;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Log;

class StudentService
{
    /**
     * 创建学生
     *  1. 创建用户
     *  2. 创建学生
     * 
     * @param Form $form
     * @throws \Exception
     */
    public function createStudent(Form $form): void
    {
        DB::beginTransaction();
        try {
            // 创建 users 记录
            $user = User::create([
                'username' => $form->username,
                'nickname' => $form->nickname,
                'email' => $form->email,
                'role' => User::ROLE_STUDENT,
                'password' => Hash::make($form->password),
            ]);

            // 创建 students 记录
            $student = new Student();
            $student->id = $user->id;
            $student->nickname = $form->nickname;
            $student->save();

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 更新学生
     *  1. 更新用户记录
     *  2. 更新学生记录
     * 
     * @param Form $form
     * @throws \Exception
     */
    public function updateStudent(Form $form): void
    {
        DB::beginTransaction();
        try {
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

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }
}
