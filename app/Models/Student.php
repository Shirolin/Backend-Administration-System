<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Student
 *
 * @property int $id
 * @property string $nickname
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $avatar
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereAvatar($value)
 */
class Student extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'id';

    protected $fillable = ['nickname'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Student $model) {
            DB::beginTransaction();
            try {
                // 删除与学生关联的用户
                $model->user()->delete();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        });
    }

    /**
     * 关联用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'id');
    }
}
