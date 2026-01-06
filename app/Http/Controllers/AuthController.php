<?php

namespace App\Http\Controllers;

use App\Helpers\ResultCode;
use App\Service\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin; // ⚠️ 注意这里引用的是 Admin

class AuthController extends Controller
{

    public function __construct(protected AdminService $adminService)
    {}
    /**
     * 登录接口
     */
    public function login(Request $request)
    {
        // 1. 验证前端传来的参数
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // 2. 尝试登录
        // 因为我们在 config/auth.php 里把 providers.users.model 改成了 Admin
        // 所以这里 Auth::attempt 会自动去查 sys_admins 表
        if (Auth::attempt($credentials)) {

            /** @var Admin $admin */
            $admin = Auth::user();

            // 检查账号状态
            if (!$admin->status) {
                Auth::logout();
                return $this->fail(ResultCode::FORBIDDEN, '账号已被禁用');
            }

            // 3. 生成 Token
            $token = $admin->createToken('admin-token')->plainTextToken;

            // 4. 返回成功数据
            return $this->success([
                'token' => $token,
                'userInfo'  => [
                    'id' => $admin->id,
                    'username' => $admin->username,
                    'name' => $admin->name,
                    'avatar' => $admin->avatar,
                ]
            ], '登录成功');
        }

        // 5. 登录失败
        return $this->fail(401, '账号或密码错误');
    }

    public function getInfo(Request $request)
    {
        $user = $request->user(); // 获取当前登录人
        $permissionsData = $this->adminService->getAdminPermissions($user->id);

        return $this->success([
            'user' => $user,
            'menus' => $permissionsData['menus'],
            'permissions' => $permissionsData['permissions']
        ]);
    }

    /**
     * 登出接口
     */
    public function logout(Request $request)
    {
        // 删除当前 Token
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, '退出成功');
    }
}
