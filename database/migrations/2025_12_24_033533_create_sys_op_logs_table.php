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
        Schema::create('sys_op_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable()->comment('操作人ID');
            $table->string('username', 50)->nullable()->comment('操作人账号');
            $table->string('ip', 45)->nullable();
            $table->string('method', 10)->nullable()->comment('GET/POST');
            $table->string('url')->nullable();
            $table->text('params')->nullable()->comment('请求参数');
            $table->integer('response_code')->nullable()->comment('响应状态码');
            $table->integer('duration')->nullable()->comment('耗时(ms)');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_op_logs');
    }
};
