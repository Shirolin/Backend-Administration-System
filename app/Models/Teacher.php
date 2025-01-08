<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
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
 */
class Teacher extends Model
{
    protected $table = 'teachers';
    protected $primaryKey = 'id';

    protected $fillable = ['nickname'];

    /**
     * 关联管理员用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminUser()
    {
        return $this->belongsTo(\Encore\Admin\Auth\Database\Administrator::class, 'admin_id', 'id');
    }

    /**
     * 关联用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id', 'id');
    }
}
