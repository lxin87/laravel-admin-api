<?php

namespace App\Helpers;

class ResultCode
{
    const SUCCESS = 200;
    const ERROR = 500;

    // 权限类
    const UNAUTHORIZED = 401;  // 未登录/Token过期
    const FORBIDDEN = 403;     // 无权限

    // 业务类 (和前端 types.ts 对应)
    const USER_NOT_FOUND = 10001;
    const PARAM_ERROR = 10002;
    const DB_ERROR = 20001;
}
