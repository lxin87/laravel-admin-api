<?php

namespace App\Models;

// 引入 Sanctum trait
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

// 继承 Authenticatable 而不是普通的 Model
class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // 1. 指定表名
    protected $table = 'sys_admins';

    // 2. 可批量赋值的字段
    protected $fillable = [
        'username', 'password', 'name', 'avatar', 'email', 'phone', 'status',
        'last_login_ip', 'last_login_time'
    ];

    // 3. 隐藏字段
    protected $hidden = [
        'password', 'remember_token',
    ];

    // 4. 类型转换
    protected $casts = [
        'password' => 'hashed',
        'last_login_time' => 'datetime'
    ];

    public function roles()
    {
        // 关联模型, 中间表名, 本模型在中间表的外键, 目标模型在中间表的外键
        return $this->belongsToMany(SysRole::class, 'sys_admin_roles', 'admin_id', 'role_id');
    }

    public function getPermissions()
    {
        // 利用 Laravel 的关联关系和集合操作，快速提取所有角色的菜单权限码
        // 假设你的 Admin 模型已经关联了 roles，且 Role 模型关联了 menus
        return $this->roles()->with('menus')->get()
            ->pluck('menus')
            ->flatten()
            ->where('status', 0)
            ->pluck('perm_code')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }
}
