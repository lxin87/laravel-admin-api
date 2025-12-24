<?php
use Illuminate\Support\Facades\Route;
use App\Helpers\ResultCode;
// 这是一个闭包路由，仅用于测试
Route::get('/test', function () {
    return response()->json([
        'code' => ResultCode::SUCCESS,
        'msg'  => 'Laravel API is working!',
        'data' => [
            'framework' => 'Laravel ' . app()->version(),
            'php' => phpversion()
        ]
    ]);
});
