<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // 获取路由中的 admin 模型实例 (Laravel 自动注入的)
        $adminId = $this->route('admin')->id;

        return [
            // 唯一性校验：排除当前 ID (unique:table,column,ignore_id)
            'username' => 'required|max:50|unique:sys_admins,username,' . $adminId,

            // 密码改为 nullable (允许为空)
            'password' => 'nullable|string|min:6',

            'name'     => 'required|string|max:50',
            'status'   => 'boolean'
        ];
    }
}
