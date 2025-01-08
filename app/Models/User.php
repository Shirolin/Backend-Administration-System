<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $email
 * @property int $role
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @property string $username
 * @property string $nickname
 * @property string|null $avatar
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    /**
     * @var int 角色-无
     */
    const ROLE_NONE = 0;
    /**
     * @var int 角色-教师
     */
    const ROLE_TEACHER = 1;
    /**
     * @var int 角色-学生
     */
    const ROLE_STUDENT = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'nickname',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 关联教师
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'id');
    }

    /**
     * 获取角色映射
     *
     * @return array
     */
    public static function getRoleMap()
    {
        return [
            self::ROLE_NONE => '无',
            self::ROLE_TEACHER => '教师',
            self::ROLE_STUDENT => '学生',
        ];
    }

    /**
     * 获取角色名称
     *
     * @return string
     */
    public function getRoleName()
    {
        return self::getRoleMap()[$this->role] ?? '';
    }

    /**
     * 是否为教师
     *
     * @return bool
     */
    public function isTeacher()
    {
        return $this->role === self::ROLE_TEACHER;
    }

    /**
     * 是否为学生
     *
     * @return bool
     */
    public function isStudent()
    {
        return $this->role === self::ROLE_STUDENT;
    }
}
