<?php

namespace App\Http\Requests\PracticeSession;

use Illuminate\Foundation\Http\FormRequest;

class CompletePracticeSessionRequest extends FormRequest
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

            'body' => ['prohibited'],
            'notes' => ['prohibited'],
            'reference_sources' => ['prohibited'],
            'status' => ['prohibited'],
            'completed_at' => ['prohibited'],
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

            'body.prohibited' =>
                'Vui lòng lưu bài trước khi hoàn thành phiên.',
            'notes.prohibited' =>
                'Vui lòng lưu ghi chú trước khi hoàn thành phiên.',
            'reference_sources.prohibited' =>
                'Vui lòng lưu tài liệu tham khảo trước khi hoàn thành phiên.',
            'status.prohibited' =>
                'Không được tự chỉ định trạng thái.',
            'completed_at.prohibited' =>
                'Không được tự chỉ định thời điểm hoàn thành.',
        ];
    }
}