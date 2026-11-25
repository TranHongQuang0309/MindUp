<?php

namespace App\Http\Requests\PracticeContent;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePersonalPracticeContentRequest extends FormRequest
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
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'sometimes',
                'required',
                'string',
                'max:10000',
            ],

            'guidance' => [
                'sometimes',
                'nullable',
                'string',
                'max:5000',
            ],

            'created_by' => ['prohibited'],
            'scope' => ['prohibited'],
            'status' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống.',
            'title.string' => 'Tiêu đề phải là chuỗi ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',

            'description.required' => 'Mô tả không được để trống.',
            'description.string' => 'Mô tả phải là chuỗi ký tự.',
            'description.max' =>
                'Mô tả không được vượt quá 10.000 ký tự.',

            'guidance.string' => 'Gợi ý phải là chuỗi ký tự.',
            'guidance.max' =>
                'Gợi ý không được vượt quá 5.000 ký tự.',

            'created_by.prohibited' =>
                'Không được thay đổi người tạo nội dung.',
            'scope.prohibited' =>
                'Không được thay đổi phạm vi qua chức năng này.',
            'status.prohibited' =>
                'Không được thay đổi trạng thái qua chức năng này.',
        ];
    }
}