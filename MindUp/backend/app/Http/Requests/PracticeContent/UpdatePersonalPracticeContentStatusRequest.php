<?php

namespace App\Http\Requests\PracticeContent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonalPracticeContentStatusRequest extends FormRequest
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
            'status' => [
                'required',
                'string',
                Rule::in(['available', 'unavailable']),
            ],

            'created_by' => ['prohibited'],
            'scope' => ['prohibited'],
            'title' => ['prohibited'],
            'description' => ['prohibited'],
            'guidance' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Vui lòng cung cấp trạng thái.',
            'status.string' => 'Trạng thái phải là chuỗi ký tự.',
            'status.in' =>
                'Trạng thái chỉ được là available hoặc unavailable.',

            'created_by.prohibited' =>
                'Không được thay đổi người tạo nội dung.',
            'scope.prohibited' =>
                'Không được thay đổi phạm vi qua chức năng này.',
            'title.prohibited' =>
                'Không được chỉnh sửa tiêu đề qua chức năng này.',
            'description.prohibited' =>
                'Không được chỉnh sửa mô tả qua chức năng này.',
            'guidance.prohibited' =>
                'Không được chỉnh sửa gợi ý qua chức năng này.',
        ];
    }
}