<?php

namespace App\Http\Requests\Auth;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge([
                'email' => mb_strtolower(trim($this->input('email'))),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:100'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                Password::min(8),
                'confirmed',
                function (
                    string $attribute,
                    mixed $value,
                    Closure $fail
                ): void {
                    if (is_string($value) && strlen($value) > 72) {
                        $fail('Mật khẩu không được vượt quá 72 byte.');
                    }
                },
            ],

            'role' => ['prohibited'],
            'status' => ['prohibited'],
            'email_verified_at' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Vui lòng nhập họ tên.',
            'full_name.string' => 'Họ tên phải là chuỗi ký tự.',
            'full_name.max' => 'Họ tên không được vượt quá 100 ký tự.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được sử dụng.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',

            'role.prohibited' => 'Không được tự chỉ định quyền tài khoản.',
            'status.prohibited' => 'Không được tự chỉ định trạng thái tài khoản.',
            'email_verified_at.prohibited' =>
                'Không được tự xác định trạng thái xác minh email.',
        ];
    }
}