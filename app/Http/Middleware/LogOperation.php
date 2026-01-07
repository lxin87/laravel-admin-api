<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LogOperation
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $response = $next($request);
        $duration = floor((microtime(true) - $startTime) * 1000);

        $routeName = $request->route() ? $request->route()->getName() : null;

        $user = $request->user();

        // 获取所有原始请求参数
        $params = $request->all();

        // 🚀 核心逻辑：剔除文件类型参数
        if ($request->hasFile('*') || !empty($request->allFiles())) {
            foreach ($request->allFiles() as $key => $file) {
                // 将文件参数替换为描述信息，而不是二进制内容
                $params[$key] = "[File: " . (is_array($file) ? 'Multiple Files' : $file->getClientOriginalName()) . "]";
            }
        }

        // 也可以根据字段名进一步过滤敏感或大字段 (可选)
        // unset($params['password'], $params['content_html']);

        \DB::table('sys_op_logs')->insert([
            'admin_id'      => $user?->id,
            'username'      => $user?->username ?? '未登录',
            'ip'            => $request->ip(),
            'method'        => $request->method(),
            'url'           => $request->fullUrl(),
            'route_name'    => $routeName,
            'params'        => json_encode($params, JSON_UNESCAPED_UNICODE),
            'response_code' => $response->getStatusCode(),
            'duration'      => $duration,
            'created_at'    => now(),
        ]);

        return $response;
    }
}
