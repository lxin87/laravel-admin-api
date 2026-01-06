<?php

namespace App\Http\Controllers;

use App\Service\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $roleService;
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index(Request $request)
    {
        // 使用我们在 Service 中定义的逻辑
        $params = $request->only(['name', 'code', 'status', 'page', 'pageSize']);

        $data = $this->roleService->getRoleList($params);

        // 返回格式要与前端 ProTable 要求的结构一致
        return $this->success($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'code' => 'required|string|unique:sys_roles,code',
            'remark' => 'nullable|string',
            'status' => 'required|in:0,1'
        ]);
        $this->roleService->saveRole($data);
        return $this->success(null, '创建成功');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:50|unique:sys_roles,code,' . $id,
            'remark' => 'nullable|string',
            'status' => 'integer|in:0,1'
        ]);
        $this->roleService->saveRole($data, (int)$id);
        return $this->success(null, '更新成功');
    }

    // 获取角色已拥有的菜单ID (关键：用于前端勾选回显)
    public function getRoleMenuIds($id)
    {
        // 调用 Service 层获取数据
        $ids = $this->roleService->getRoleMenuIds((int)$id);
        return $this->success($ids);
    }

    // 分配权限
    public function assignMenus(Request $request, $id)
    {
        $role = \App\Models\SysRole::findOrFail($id);
        $this->roleService->assignPermissions($role, $request->menu_ids);
        return $this->success(null, '权限分配成功');
    }

    /**
     * 更新角色状态
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:0,1'
        ]);

        $this->roleService->updateStatus((int)$id, (int)$request->status);

        return $this->success(null, '状态更新成功');
    }

    public function destroy($id)
    {
        try {
            $role = \App\Models\SysRole::findOrFail($id);
            if ($role->code === 'SUPER_ADMIN') {
                return $this->fail(600,'系统核心角色，禁止删除');
            }

            $this->roleService->deleteRole((int)$id);
            return $this->success(null, '删除成功');
        } catch (\Exception $e) {
            // 返回 Service 中抛出的具体错误信息
            return $this->fail(600,$e->getMessage());
        }
    }

}
