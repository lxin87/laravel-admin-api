<?php

namespace App\Service;

use App\Models\Admin;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    /**
     * 获取管理员分页列表
     * @param array $params 查询参数
     * @return LengthAwarePaginator
     */
    public function getAdminList(array $params): LengthAwarePaginator
    {
        // 1. 初始化查询
        $query = Admin::query();

        // 2. 搜索逻辑 (如果有 username 参数)
        if (!empty($params['username'])) {
            $query->where('username', 'like', '%' . $params['username'] . '%');
        }

        $query->with(['roles:id,name']);

        // 3. 排序 (默认按创建时间倒序)
        $query->orderBy('created_at', 'desc');

        // 4. 分页返回 (默认每页 10 条)
        $pageSize = $params['pageSize'] ?? 10;

        return $query->paginate($pageSize);
    }

    /**
     * 创建管理员
     */
    public function createAdmin(array $data): Admin
    {
        // 1. 密码加密
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // 2. 创建记录
        return Admin::create($data);
    }

    /**
     * 更新管理员
     */
    public function updateAdmin(Admin $admin, array $data): bool
    {
        // 核心逻辑：如果没有填密码，就把它从数组里剔除，防止被覆盖为空字符串
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        return $admin->update($data);
    }

    /**
     * 获取管理员拥有的角色ID集合
     */
    public function getAdminRoleIds(int $adminId): array
    {
        $user = \App\Models\Admin::findOrFail($adminId);
        return $user->roles()->pluck('sys_roles.id')->toArray();
    }

    /**
     * 为管理员分配角色
     */
    public function assignRoles(int $adminId, array $roleIds)
    {
        $user = \App\Models\Admin::findOrFail($adminId);
        // sync 方法会自动维护 sys_admin_roles 表
        return $user->roles()->sync($roleIds);
    }
    /**
     * 获取当前登录管理员的权限信息
     */
    public function getAdminPermissions($adminId)
    {
        $user = \App\Models\Admin::findOrFail($adminId);

        // 1. 获取用户所有的菜单ID (保持不变)
        $menuIds = \DB::table('sys_admin_roles as ar')
            ->join('sys_role_menus as rm', 'ar.role_id', '=', 'rm.role_id')
            ->where('ar.admin_id', $adminId)
            ->distinct()
            ->pluck('rm.menu_id');

        // 2. 获取所有权限记录 (包括目录、菜单、按钮，不限制 type)
        // 只有拿到全部数据，permissions 才是完整的
        $allMenus = \App\Models\SysMenu::whereIn('id', $menuIds)
            ->where('status', 0)
            ->orderBy('sort', 'desc')
            ->get();

        // 3. 提取完整的权限标识符 (包含按钮 type=3 的 code)
        $permissions = $allMenus->pluck('perm_code')->filter()->unique()->values()->toArray();

        // 4. 将用于“展示侧边栏”的菜单过滤出来 (只需要目录和菜单)
        $showMenus = $allMenus->whereIn('type', [1, 2]);
        $menuTree = $this->buildMenuTree($showMenus->toArray());

        return [
            'menus' => $menuTree,
            'permissions' => $permissions
        ];
    }

    /**
     * 辅助方法：构建菜单树
     */
    private function buildMenuTree(array $nodes, $parentId = 0)
    {
        $tree = [];
        foreach ($nodes as $node) {
            if ($node['parent_id'] == $parentId) {
                $children = $this->buildMenuTree($nodes, $node['id']);
                $node['children'] = $children;
                $tree[] = $node;
            }
        }
        return $tree;
    }
}
