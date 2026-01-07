<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OperationLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login'])->middleware('opt.log');
//TODO::优化权限层级问题，目前只是把一些跟CURD无关的权限放在一个公共权限组下，并且设置为隐藏。
//但很多时候，一个按钮权限下，可能有多个接口权限，如果都仍到公共权限组下，时间久了可能就分不清是哪里的权限了。后续优化下

Route::middleware(['auth:sanctum','auth.check','opt.log'])->group(function () {
    Route::get('auth/info', [AuthController::class, 'getInfo'])->name('sys:auth:info');
    Route::post('/logout', [AuthController::class, 'logout'])->name('sys:logout');

    // ▼▼▼ 新增路由 ▼▼▼
    // 使用资源路由 apiResource 可以自动生成 index, store, show, update, destroy 路由
    Route::apiResource('admins', AdminController::class)->names('sys:admin');
    Route::get('admins/{id}/role-ids', [AdminController::class, 'getRoleIds'])->name('sys:admin:getRoleIds');
    Route::post('admins/{id}/roles', [AdminController::class, 'assignRoles'])->name('sys:admin:assignRole');

    // 注册菜单资源路由
    Route::apiResource('menus', MenuController::class)->names('sys:menu');

    Route::apiResource('roles', RoleController::class)->names('sys:role');
    Route::post('roles/{role}/menus', [RoleController::class, 'assignMenus'])->name('sys:role:assignPermission');
    // 角色的状态更新 (注意：放在 apiResource 之前或之后都可以，只要路径不冲突)
    Route::patch('roles/{role}/status', [RoleController::class, 'updateStatus'])->name('sys:role:updateStatus');
    // 获取角色已拥有的菜单ID
    Route::get('roles/{role}/menu-ids', [RoleController::class, 'getRoleMenuIds'])->name('sys:role:getRoleMenuIds');

    //修改个人资料
    Route::put('profile/update', [ProfileController::class, 'update'])->name('sys:profile:update');

    //操作日志
    Route::get('system/logs', [OperationLogController::class, 'index'])->name('sys:log:index');
});
