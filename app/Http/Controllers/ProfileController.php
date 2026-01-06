<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        $messages = [
            'username.max' => '用户名太长了，不能超过20个字符',
            'phone.unique' => '该手机号已被他人绑定',
            'phone.size'   => '手机号必须是11位数字',
            'password.confirmed' => '两次输入的密码不匹配',
            'password.min' => '新密码至少需要6位',
        ];

        // 1. 手动创建验证器
        $validator = Validator::make($request->all(), [
            'username' => 'string|max:20',
            'name'     => 'string|max:20',
            'avatar'   => 'string|nullable',
            'phone'    => [
                'string',
                'size:11',
                Rule::unique('sys_admins', 'phone')->ignore($user->id)
            ],
            'password' => 'nullable|min:6|confirmed',
        ],$messages);

        // 2. 检查是否失败
        if ($validator->fails()) {
            // 获取第一条错误消息并使用你封装的 error 方法返回
            $errorMsg = $validator->errors()->first();
            // 假设你的基类有 error($message, $code = 400) 方法
            return $this->fail(600,$errorMsg);
        }

        // 3. 验证通过，获取数据
        $data = $validator->validated();

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
            unset($data['password_confirmation']);
        }

        $user->update($data);

        return $this->success($user, '更新资料成功');
    }

}
