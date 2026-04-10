<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:15'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc,dns', // 👈 关键修改：更严格的邮箱验证
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => ['required', 'confirmed', 'min:8'],
        ];
    }
        public function messages(): array
        {
            return [
                'name.required' => '姓名不能为空',
                'name.string'   => '姓名必须是字符串',
                'name.max'      => '姓名不能超过15个字符',

                'email.required' => '邮箱不能为空',
                'email.email'    => '请输入有效的邮箱地址',
                'email.unique'   => '该邮箱已被注册',
                'email.lowercase'=> '邮箱必须为小写',

                'password.required'  => '密码不能为空',
                'password.confirmed' => '两次输入的密码不一致',
                'password.min'       => '密码至少需要8位',
            ];
        }

    protected function getRedirectUrl()
    {
        return url()->previous();
    }
}