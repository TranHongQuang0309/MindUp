<?php

namespace App\Http\Requests\PracticeContent;

use Illuminate\Foundation\Http\FormRequest;

class IndexPracticeContentRequest extends FormRequest
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
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'page.integer' => 'Số trang phải là số nguyên.',
            'page.min' => 'Số trang phải từ 1 trở lên.',
            'per_page.integer' => 'Số nội dung mỗi trang phải là số nguyên.',
            'per_page.min' => 'Mỗi trang phải có ít nhất 1 nội dung.',
            'per_page.max' => 'Mỗi trang không được vượt quá 50 nội dung.',
        ];
    }
}