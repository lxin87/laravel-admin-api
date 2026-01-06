<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/info', [AuthController::class, 'getInfo']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ▼▼▼ 新增路由 ▼▼▼
    // 使用资源路由 apiResource 可以自动生成 index, store, show, update, destroy 路由
    Route::apiResource('admins', AdminController::class);
    Route::get('admins/{id}/role-ids', [AdminController::class, 'getRoleIds']);
    Route::post('admins/{id}/roles', [AdminController::class, 'assignRoles']);

    // 注册菜单资源路由
    Route::apiResource('menus', MenuController::class);

    Route::apiResource('roles', RoleController::class);
    Route::post('roles/{role}/menus', [RoleController::class, 'assignMenus']);
    // 角色的状态更新 (注意：放在 apiResource 之前或之后都可以，只要路径不冲突)
    Route::patch('roles/{role}/status', [RoleController::class, 'updateStatus']);
    // 获取角色已拥有的菜单ID
    Route::get('roles/{role}/menu-ids', [RoleController::class, 'getRoleMenuIds']);

    //修改个人资料
    Route::put('profile/update', [ProfileController::class, 'update']);
});
