<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. 角色表
        Schema::create('sys_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('角色名称');
            $table->string('code', 50)->unique()->comment('角色标识');
            $table->string('remark')->nullable()->comment('备注');
            $table->boolean('status')->default(1)->comment('状态');
            $table->timestamps();
        });

        // 2. 菜单权限表
        Schema::create('sys_menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父ID');
            $table->string('title', 50)->comment('菜单名称');
            $table->string('icon', 50)->nullable()->comment('图标');
            $table->string('path', 100)->nullable()->comment('路由路径');
            $table->string('component', 100)->nullable()->comment('组件路径');
            $table->string('perm_code', 100)->nullable()->comment('权限标识');
            $table->tinyInteger('type')->default(1)->comment('1:目录 2:菜单 3:按钮');
            $table->integer('sort')->default(0)->comment('排序');
            $table->boolean('status')->default(0)->comment('是否隐藏');
            $table->timestamps();
        });

        // 3. 关联表：管理员 - 角色
        Schema::create('sys_admin_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('role_id');
            $table->primary(['admin_id', 'role_id']);
        });

        // 4. 关联表：角色 - 菜单
        Schema::create('sys_role_menus', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('menu_id');
            $table->primary(['role_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_role_menus');
        Schema::dropIfExists('sys_admin_roles');
        Schema::dropIfExists('sys_menus');
        Schema::dropIfExists('sys_roles');
    }
};
