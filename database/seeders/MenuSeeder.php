<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SysMenu;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 清空表（可选，开发阶段常用）
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SysMenu::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. 一级目录：系统管理
        $system = SysMenu::create([
            'parent_id' => 0,
            'title'     => '系统管理',
            'icon'      => 'Setting',
            'path'      => '/system',
            'component' => 'Layout',
            'type'      => 1, // 目录
            'sort'      => 1,
            'status'    => 0  // 显示
        ]);

        // 2. 二级菜单：管理员管理
        $admin = SysMenu::create([
            'parent_id' => $system->id,
            'title'     => '管理员管理',
            'icon'      => 'User',
            'path'      => 'admin', // 实际访问路径为 /system/admin
            'component' => 'system/admin/index',
            'perm_code' => 'system:admin:list',
            'type'      => 2, // 菜单
            'sort'      => 1,
            'status'    => 0
        ]);

        // 3. 三级按钮：管理员管理下的各种操作
        $btns = [
            ['title' => '查看', 'code' => 'system:admin:query', 'sort' => 1],
            ['title' => '新增', 'code' => 'system:admin:add',   'sort' => 2],
            ['title' => '编辑', 'code' => 'system:admin:edit',  'sort' => 3],
            ['title' => '删除', 'code' => 'system:admin:delete','sort' => 4],
        ];

        foreach ($btns as $btn) {
            SysMenu::create([
                'parent_id' => $admin->id,
                'title'     => $btn['title'],
                'perm_code' => $btn['code'],
                'type'      => 3, // 按钮
                'sort'      => $btn['sort'],
                'status'    => 0
            ]);
        }

        // 4. 二级菜单：菜单管理
        SysMenu::create([
            'parent_id' => $system->id,
            'title'     => '菜单管理',
            'icon'      => 'Menu',
            'path'      => 'menu',
            'component' => 'system/menu/index',
            'perm_code' => 'system:menu:list',
            'type'      => 2,
            'sort'      => 2,
            'status'    => 0
        ]);

        $this->command->info('菜单种子数据填充完成！');
    }
}
