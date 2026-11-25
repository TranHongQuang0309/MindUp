<?php

namespace App\Http\Requests\PracticeSession;

use Illuminate\Foundation\Http\FormRequest;

class DeletePracticeSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'revision' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'revision.required' =>
                'Thiếu số thứ tự cập nhật của phiên.',
            'revision.integer' =>
                'Số thứ tự cập nhật phải là số nguyên.',
            'revision.min' =>
                'Số thứ tự cập nhật không được nhỏ hơn 0.',
        ];
    }
}