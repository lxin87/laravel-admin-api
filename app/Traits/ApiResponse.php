<?php

namespace App\Traits;

use App\Helpers\ResultCode;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * 成功响应
     * @param mixed $data 数据
     * @param string $msg 提示信息
     * @return JsonResponse
     */
    public function success($data = null, $msg = '操作成功'): JsonResponse
    {
        return response()->json([
            'code' => ResultCode::SUCCESS,
            'msg'  => $msg,
            'data' => $data
        ]);
    }

    /**
     * 失败响应
     * @param int $code 业务错误码 (非 HTTP 状态码)
     * @param string $msg 错误提示
     * @param mixed $data 额外数据
     * @return JsonResponse
     */
    public function fail($code = ResultCode::ERROR, $msg = '操作失败', $data = null): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ]);
        // 注意：这里 HTTP 状态码依然保持 200，这也是一种常见的做法，
        // 由 code 字段控制前端逻辑。如果你喜欢 RESTful，可以改为 response()->json(..., $httpCode);
    }
}
