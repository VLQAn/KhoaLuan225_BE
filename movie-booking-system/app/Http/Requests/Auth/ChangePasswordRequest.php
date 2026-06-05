<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'oldPassword' => ['required'],
            'newPassword' => ['required', 'min:6'],
            'confirmPassword' => ['required', 'same:newPassword'],
        ];
    }

    public function messages(): array
    {
        return [
            'confirmPassword.same'
                => 'Xác nhận mật khẩu không khớp',
        ];
    }
}
