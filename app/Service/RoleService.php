<?php

namespace App\Service;

use App\Models\SysRole;

class RoleService
{
    // 获取角色列表（分页）
    public function getRoleList($params)
    {
        return SysRole::when($params['name'] ?? null, function ($query, $name) {
            $query->where('name', 'like', "%{$name}%");
        })
            ->when($params['code'] ?? null, function ($query, $code) {
                $query->where('code', 'like', "%{$code}%");
            })
            ->orderBy('id', 'desc')
            ->paginate($params['pageSize'] ?? 10);
    }

    /**
     * 保存或更新角色
     */
    public function saveRole(array $data, $id = null)
    {
        if ($id) {
            $role = \App\Models\SysRole::findOrFail($id);
            return $role->update($data);
        }
        return \App\Models\SysRole::create($data);
    }
    // 分配权限的核心逻辑
    public function assignPermissions(SysRole $role, array $menuIds)
    {
        // sync 是处理多对多关联的神器：
        // 它会自动对比数据库，多出来的删除，少的添加，保持一致
        return $role->menus()->sync($menuIds);
    }

    public function getRoleMenuIds(int $roleId): array
    {
        $role = \App\Models\SysRole::findOrFail($roleId);
        return $role->menus()->pluck('sys_menus.id')->toArray();
    }

    /**
     * 切换角色状态
     */
    public function updateStatus(int $id, int $status)
    {
        $role = \App\Models\SysRole::findOrFail($id);
        $role->status = $status;
        return $role->save();
    }

    /**
     * 删除角色
     */
    public function deleteRole(int $id)
    {
        $role = \App\Models\SysRole::findOrFail($id);

        // 1. 检查是否有关联的权限（菜单）
        // exists() 比 count() 更快，因为它只要找到一条就返回
        if ($role->menus()->exists()) {
            throw new \Exception('该角色已分配权限，请先清空其权限后再删除');
        }

        // 2. 额外建议：检查是否有关联的用户
        // 如果该角色正被某些管理员使用，直接删除会导致这些管理员权限丢失
         if ($role->admins()->exists()) {
            throw new \Exception('该角色下有管理员，禁止删除');
         }

         //3.检查是否是超级管理员
        if($role->id === \App\Models\SysRole::SUPER_ADMIN_ID){
            throw new \Exception('管理员角色，禁止删除');
        }

        return $role->delete();
    }
}
