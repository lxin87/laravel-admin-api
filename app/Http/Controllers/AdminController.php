<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use App\Service\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // 注入 Service
    public function __construct(protected AdminService $adminService)
    {}

    /**
     * 获取列表
     */
    public function index(Request $request)
    {
        // 获取前端传来的 page, pageSize, username 等
        $params = $request->all();
        //TODO::显示管理员对应的角色信息
        $data = $this->adminService->getAdminList($params);

        return $this->success($data);
    }

    /**
     * 新增管理员
     * 依赖注入 StoreAdminRequest，Laravel 会自动先验证，验证不通过直接抛异常返回前端
     */
    public function store(StoreAdminRequest $request)
    {
        // 验证通过的数据
        $data = $request->validated();

        $this->adminService->createAdmin($data);

        return $this->success(null, '创建成功');
    }

    /**
     * 更新
     * 注意参数顺序：Request 在前，Model 在后 (路由模型绑定)
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $data = $request->validated();

        $this->adminService->updateAdmin($admin, $data);

        return $this->success(null, '更新成功');
    }

    /**
     * 删除管理员
     */
    public function destroy(Admin $admin)
    {
        // 可以在这里加个判断：不能删除自己
        if ($admin->id === auth()->id()) {
            return $this->fail(null,'不能删除自己'); // 假设你有 fail 方法，或者直接 abort(403)
        }

        $admin->delete();
        return $this->success(null, '删除成功');
    }

    /**
     * 获取管理员的角色ID列表
     */
    public function getRoleIds($id)
    {
        $ids = $this->adminService->getAdminRoleIds((int)$id);
        return $this->success($ids);
    }

    /**
     * 给管理员分配角色
     */
    public function assignRoles(Request $request, $id)
    {
        $request->validate([
            'role_ids' => 'array'
        ]);

        // 1. 强制类型转换，确保 ID 匹配
        $id = (int)$id;
        $roleIds = $request->role_ids;

        // 2. 后端安全校验：防止取消 ID 为 1 的超级管理员的所有角色
        // 这里的 1 对应你系统预留的超级管理员用户 ID
        if ($id === 1 && empty($roleIds)) {
            return $this->fail(600,'为了系统安全，禁止取消超级管理员的所有角色');
        }

        $this->adminService->assignRoles((int)$id, $request->role_ids);
        return $this->success(null, '角色分配成功');
    }
}
