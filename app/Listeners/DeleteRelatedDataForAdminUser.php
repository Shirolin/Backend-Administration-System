<?php

namespace App\Listeners;

use App\Models\Teacher;
use App\Models\User;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteRelatedDataForAdminUser
{
    /**
     * 管理员用户删除事件处理
     *
     * @param Administrator $user
     * @return void
     */
    public function handle(Administrator $user)
    {
        Log::info('DeleteRelatedDataForAdminUser', ['user' => $user]);
        DB::beginTransaction();
        try {
            $teacher = Teacher::where('admin_id', $user->id)->first();

            if ($teacher) {
                $teacherId = $teacher->id;
                
                // 正确删除教师
                $teacher->delete();

                // 正确删除关联用户
                User::where('id', $teacherId)->delete();
                DB::table('users')->where('id', $teacherId)->delete();

                Log::info('DeleteRelatedDataForAdminUser', ['teacher' => $teacher]);
            }

            // 如果是多对多关系，使用 detach()
            $user->roles()->detach();
            $user->permissions()->detach();

            Log::info('DeleteRelatedDataForAdminUser', ['teacher' => $teacher]);

            DB::commit();
        } catch (\Exception $e) {
            Log::error('DeleteRelatedDataForAdminUser', ['exception' => $e]);
            DB::rollback();
            throw $e;
        }
    }
}
