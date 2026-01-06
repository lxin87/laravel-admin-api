<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SysRole extends Model
{
    protected $fillable = ['name', 'code', 'remark', 'status'];

    // 定义超级管理员 ID 常量
    const SUPER_ADMIN_ID = 1;

    // 定义与菜单的多对多关联
    public function menus()
    {
        // 关联模型, 中间表名, 本模型在中间表的外键, 目标模型在中间表的外键
        return $this->belongsToMany(SysMenu::class, 'sys_role_menus', 'role_id', 'menu_id');
    }

    /**
     * 关联管理员（User模型）
     * 建立 Role -> User 的反向多对多关联
     */
    public function admins()
    {
        // 参数说明：
        // 1. \App\Models\User::class : 关联的目标模型
        // 2. 'sys_admin_roles' : 中间表名
        // 3. 'role_id' : 本模型(SysRole)在中间表中的外键
        // 4. 'admin_id' : 目标模型(User)在中间表中的外键
        return $this->belongsToMany(\App\Models\Admin::class, 'sys_admin_roles', 'role_id', 'admin_id');
    }
}
