<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    use \App\Traits\ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission = null): Response
    {
        $user = $request->user();
        //if ($user->id === 1) return $next($request);


        // 1. 如果路由定义里传了参数，优先使用参数
        // 2. 如果没传，自动获取路由别名 (例如 sys:role.index)
        $routePermission = $permission ?: $request->route()->getName();

        // 💡 关键：将路由别名的点(.)转换为冒号(:)，以匹配你的数据库规范
        // 例如：sys:role.index -> sys:role:index
        $requiredPerm = str_replace('.', ':', $routePermission);

        $userPermissions = $user->getPermissions();


        if (!in_array($requiredPerm, $userPermissions)) {

            return $this->fail(403, "您无权访问");
        }

        return $next($request);
    }
}
