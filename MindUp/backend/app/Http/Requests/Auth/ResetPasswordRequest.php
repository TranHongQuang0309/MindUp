<?php

namespace App\Http\Requests\Auth;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
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
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],

            'token' => [
                'required',
                'string',
                'max:255',
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

            'password_confirmation' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Vui lòng cung cấp email.',
            'email.string' => 'Email phải là chuỗi ký tự.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',

            'token.required' => 'Thiếu mã đặt lại mật khẩu.',
            'token.string' => 'Mã đặt lại mật khẩu phải là chuỗi ký tự.',
            'token.max' => 'Mã đặt lại mật khẩu vượt quá độ dài cho phép.',

            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',

            'password_confirmation.required' =>
                'Vui lòng nhập lại mật khẩu mới.',
            'password_confirmation.string' =>
                'Mật khẩu xác nhận phải là chuỗi ký tự.',
        ];
    }
}