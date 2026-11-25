<?php

namespace App\Http\Requests\PracticeSession;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePracticeSessionRequest extends FormRequest
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
            'practice_content_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'target_duration_seconds' => [
                'nullable',
                'integer',
                Rule::in([300, 600, 900]),
            ],

            'user_id' => ['prohibited'],
            'status' => ['prohibited'],
            'started_at' => ['prohibited'],
            'revision' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'practice_content_id.required' =>
                'Vui lòng chọn nội dung luyện tập.',
            'practice_content_id.integer' =>
                'ID nội dung luyện tập phải là số nguyên.',
            'practice_content_id.min' =>
                'ID nội dung luyện tập không hợp lệ.',

            'target_duration_seconds.integer' =>
                'Thời gian mục tiêu phải là số nguyên.',
            'target_duration_seconds.in' =>
                'Thời gian mục tiêu phải là 5, 10 hoặc 15 phút.',

            'user_id.prohibited' =>
                'Không được tự chỉ định tài khoản sở hữu phiên.',
            'status.prohibited' =>
                'Không được tự chỉ định trạng thái phiên.',
            'started_at.prohibited' =>
                'Không được tự chỉ định thời điểm bắt đầu.',
            'revision.prohibited' =>
                'Không được tự chỉ định số lần cập nhật.',
        ];
    }
}