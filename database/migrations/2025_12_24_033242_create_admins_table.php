<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sys_admins', function (Blueprint $table) {
            $table->id();
            $table->string('username', 60)->unique()->comment('登录账号');
            $table->string('password');
            $table->string('name', 50)->comment('姓名/昵称');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('status')->default(1)->comment('状态: 1启用 0禁用');
            $table->string('last_login_ip', 45)->nullable();
            $table->timestamp('last_login_time')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
