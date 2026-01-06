<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ⚠️ 改为 true，否则会报 403
    }

    public function rules(): array
    {
        return [
            // unique:表名,字段名 -> 确保账号唯一
            'username' => 'required|string|max:50|unique:sys_admins,username',
            'password' => 'required|string|min:6',
            'name'     => 'required|string|max:50',
            'avatar'   => 'nullable|string',
            'status'   => 'boolean' // 允许直接传状态
        ];
    }

    // 自定义错误提示 (可选，Laravel 默认也是英文/中文提示)
    public function messages()
    {
        return [
            'username.unique' => '该账号已存在',
            'password.min'    => '密码长度不能少于6位'
        ];
    }
}
