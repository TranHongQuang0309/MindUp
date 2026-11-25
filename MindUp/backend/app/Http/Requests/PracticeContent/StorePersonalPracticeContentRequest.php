<?php

namespace App\Http\Requests\PracticeContent;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonalPracticeContentRequest extends FormRequest
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
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
                'max:10000',
            ],

            'guidance' => [
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
            'title.required' => 'Vui lòng nhập tiêu đề nội dung luyện tập.',
            'title.string' => 'Tiêu đề phải là chuỗi ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',

            'description.required' =>
                'Vui lòng nhập mô tả nội dung luyện tập.',
            'description.string' => 'Mô tả phải là chuỗi ký tự.',
            'description.max' =>
                'Mô tả không được vượt quá 10.000 ký tự.',

            'guidance.string' => 'Gợi ý phải là chuỗi ký tự.',
            'guidance.max' => 'Gợi ý không được vượt quá 5.000 ký tự.',

            'created_by.prohibited' =>
                'Không được tự chỉ định người tạo nội dung.',
            'scope.prohibited' =>
                'Không được tự chỉ định phạm vi nội dung.',
            'status.prohibited' =>
                'Không được tự chỉ định trạng thái khi tạo nội dung.',
        ];
    }
}