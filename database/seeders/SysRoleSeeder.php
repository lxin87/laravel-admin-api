<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SysRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\SysRole::create([
            'name' => '超级管理员',
            'code' => 'SUPER_ADMIN',
            'remark' => '拥有系统所有权限',
            'status' => 1
        ]);

        \App\Models\SysRole::create([
            'name' => '普通运营',
            'code' => 'OPERATOR',
            'remark' => '负责日常内容维护',
            'status' => 1
        ]);
    }
}
