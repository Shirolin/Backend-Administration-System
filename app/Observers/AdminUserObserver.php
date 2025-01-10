<?php

namespace App\Observers;

use App\Models\Teacher;
use App\Models\User;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Support\Facades\Log;

class AdminUserObserver
{
    public function saving(Administrator $user)
    {
        // Log::info('即将保存管理员到数据库:' . json_encode($user));
    }

    public function creating(Administrator $user)
    {
        // Log::info('即将插入管理员到数据库:' . json_encode($user));
    }

    public function updating(Administrator $user)
    {
        // Log::info('即将更新管理员到数据库:' . json_encode($user));
    }

    public function updated(Administrator $user)
    {
        // Log::info('已经更新管理员到数据库:' . json_encode($user));
    }

    public function created(Administrator $user)
    {
        // Log::info('已经插入管理员到数据库:' . json_encode($user));
    }

    public function saved(Administrator $user)
    {
        // Log::info('已经保存管理员到数据库:' . json_encode($user));
    }

    public function deleting(Administrator $user)
    {
        // 管理员删除时，一并删除关联的教师，以及和教师关联的用户
        Log::info('即将删除管理员到数据库:' . json_encode($user));
        $userId = $user->id;
        $teacher = Teacher::where('admin_id', $userId)->first();
        if ($teacher) {
            User::where('id', $teacher->id)->delete();
            $teacher->delete();
            Log::info('删除教师和用户成功');
        }
    }

    public function deleted(Administrator $user)
    {
        // Log::info('已经删除管理员到数据库:' . json_encode($user));
    }
}
