<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * 教师
 * App\Models\Teacher
 *
 * @property int $id
 * @property string $nickname
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Encore\Admin\Auth\Database\Administrator $adminUser
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereUpdatedAt($value)
 * @property int $admin_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Teacher whereAdminId($value)
 * @mixin \Eloquent
 * @property string|null $avatar
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Teacher whereAvatar($value)
 */
class Teacher extends Model
{
    protected $table = 'teachers';
    protected $primaryKey = 'id';

    protected $fillable = ['nickname', 'admin_id'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Teacher $model) {
            DB::beginTransaction();
            try {
                // 删除与教师关联的管理员用户
                $model->adminUser()->delete();

                // 删除与教师关联的用户
                $model->user()->delete();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        });
    }

    /**
     * 关联管理员用户
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(\Encore\Admin\Auth\Database\Administrator::class, 'admin_id', 'id');
    }

    /**
     * 关联用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
