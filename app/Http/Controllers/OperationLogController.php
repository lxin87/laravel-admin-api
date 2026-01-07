<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OperationLogController extends Controller
{
    public function index(Request $request)
    {
        $query = \DB::table('sys_op_logs');

        // 1. 账号筛选
        if ($request->filled('username')) {
            $query->where('username', 'like', '%' . $request->username . '%');
        }

        if ($request->filled('route_name')) {
            $query->where('route_name', 'like', '%' . $request->route_name . '%');
        }

        // 2. 🚀 时间区间查询逻辑
        $createdAt = $request->input('created_at');
        if (is_array($createdAt) && count($createdAt) === 2) {
            // 处理前端传来的数组格式 [2026-01-01, 2026-01-02]
            $query->whereBetween('created_at', [
                $createdAt[0] . ' 00:00:00',
                $createdAt[1] . ' 23:59:59'
            ]);
        }

        $list = $query->orderBy('id', 'desc')
            ->paginate($request->input('limit', 15));

        return $this->success($list);
    }
}
