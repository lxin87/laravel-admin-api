<?php

namespace App\Http\Controllers;

use App\Models\SysMenu;
use App\Service\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{

    protected $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }
    public function index()
    {
        $data = $this->menuService->getMenuTree();
        return $this->success($data);
    }

    public function store(Request $request) {
        // 实际开发中也可以像管理员模块那样抽离出 StoreMenuRequest
        $validData = $request->validate([
            'parent_id' => 'required|integer',
            'title'     => 'required|string|max:50',
            'type'      => 'required|integer|in:1,2,3',
            'path'      => 'nullable|string|max:100',
            'component' => 'nullable|string|max:100',
            'perm_code' => 'nullable|string|max:100',
            'icon'      => 'nullable|string|max:50',
            'sort'      => 'integer',
            'is_hidden' => 'integer',
            'status'    => 'integer'
        ]);

        $menu = $this->menuService->createMenu($validData);

        return $this->success($menu, '菜单创建成功');
    }

    public function update(Request $request, SysMenu $menu)
    {
        // 这里可以使用和 store 一样的校验逻辑，或者抽离出 UpdateMenuRequest
        $validData = $request->validate([
            'parent_id' => 'required|integer',
            'title'     => 'required|string|max:50',
            'type'      => 'required|integer|in:1,2,3',
            'path'      => 'nullable|string|max:100',
            'component' => 'nullable|string|max:100',
            'perm_code' => 'nullable|string|max:100',
            'icon'      => 'nullable|string|max:50',
            'sort'      => 'integer',
            'is_hidden' => 'integer',
            'status'    => 'integer'
        ]);

        $this->menuService->updateMenu($menu, $validData);

        return $this->success(null, '菜单更新成功');
    }

    public function destroy(SysMenu $menu)
    {
        try {
            $this->menuService->deleteMenu($menu);
            return $this->success(null, '菜单删除成功');
        } catch (\Exception $e) {
            // 捕获 Service 抛出的异常并返回给前端
            return $this->fail(600,$e->getMessage());
        }
    }
}
