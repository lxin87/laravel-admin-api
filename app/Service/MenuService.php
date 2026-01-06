<?php

namespace App\Service;

use App\Models\SysMenu;

class MenuService
{
    /**
     * 获取树形菜单列表
     */
    public function getMenuTree()
    {
        // 只获取顶级菜单，然后通过关联加载无限级的子菜单
        return SysMenu::where('parent_id', 0)
            ->with('children.children') // 这里的 children 也会递归加载它自己的 children
            ->orderBy('sort', 'asc')
            ->get();
    }

    /**
     * 创建菜单
     * 遵循 Service 处理业务逻辑的原则
     */
    public function createMenu(array $data)
    {
        // 如果 parent_id 不是 0，查一下父级
        if ($data['parent_id'] !== 0) {
            $parent = SysMenu::find($data['parent_id']);
            // 如果父级是按钮，禁止创建
            if ($parent && $parent->type === 3) {
                throw new \Exception('不能在按钮下创建子项');
            }
        }
        return SysMenu::create($data);
    }

    /**
     * 更新菜单
     */
    public function updateMenu(SysMenu $menu, array $data)
    {
        // 防御性校验：不能把父级设为自己
        if (isset($data['parent_id']) && $data['parent_id'] == $menu->id) {
            throw new \Exception('上级菜单不能是当前菜单自己');
        }
        /**
         * TODO: 深度层级校验逻辑
         * 场景：当 $menu->type 被改变，或者 $data['parent_id'] 被改变时
         * 校验逻辑：
         * - 如果 $data['type'] == 1 (目录)，其 parent 必须 type=1 或 parent_id=0
         * - 如果 $data['type'] == 2 (菜单)，其 parent 必须 type=1
         * - 如果 $data['type'] == 3 (按钮)，其 parent 必须 type=2
         * - 还要考虑如果当前节点有子节点，改变当前节点类型是否会导致子节点逻辑非法
         */

        return $menu->update($data);
    }

    /**
     * 删除菜单
     * @param SysMenu $menu
     * @return bool
     * @throws \Exception
     */
    public function deleteMenu(SysMenu $menu)
    {
        // 1. 检查是否存在子项（包括子菜单和按钮）
        $hasChildren = SysMenu::where('parent_id', $menu->id)->exists();

        if ($hasChildren) {
            // 如果有子项，抛出异常，阻止删除
            throw new \Exception('该菜单下存在子级菜单或按钮权限，请先删除子项后再试');
        }

        return $menu->delete();
    }
}
