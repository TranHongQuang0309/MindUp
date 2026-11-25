<?php

namespace App\Http\Requests\PracticeSession;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexPracticeSessionRequest extends FormRequest
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

            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:50',
            ],

            'status' => [
                'sometimes',
                'string',
                Rule::in(['in_progress', 'completed']),
            ],

            'user_id' => ['prohibited'],
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

            'per_page.integer' => 'Số phiên mỗi trang phải là số nguyên.',
            'per_page.min' => 'Mỗi trang phải có ít nhất một phiên.',
            'per_page.max' => 'Mỗi trang không được vượt quá 50 phiên.',

            'status.string' => 'Trạng thái phải là chuỗi ký tự.',
            'status.in' =>
                'Trạng thái chỉ được là in_progress hoặc completed.',

            'user_id.prohibited' =>
                'Không được chỉ định tài khoản khác để xem lịch sử.',
        ];
    }
}