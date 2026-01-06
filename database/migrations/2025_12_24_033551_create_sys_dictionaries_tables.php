<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 主表
        Schema::create('sys_dictionaries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('字典名称');
            $table->string('code', 50)->unique()->comment('字典编码');
            $table->boolean('status')->default(1);
            $table->string('remark')->nullable();
            $table->timestamps();
        });

        // 子表
        Schema::create('sys_dict_items', function (Blueprint $table) {
            $table->id();
            $table->string('dict_code', 50)->index()->comment('关联编码');
            $table->string('label', 50)->comment('标签');
            $table->string('value', 50)->comment('键值');
            $table->integer('sort')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_dict_items');
        Schema::dropIfExists('sys_dictionaries');
    }
};
