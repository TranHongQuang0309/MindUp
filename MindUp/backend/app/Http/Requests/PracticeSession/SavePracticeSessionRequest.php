<?php

namespace App\Http\Requests\PracticeSession;

use Illuminate\Foundation\Http\FormRequest;

class SavePracticeSessionRequest extends FormRequest
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
            'body' => ['sometimes','required', 'string', 'max:50000'],

            'user_id' => ['prohibited'],
            'practice_content_id' => ['prohibited'],
            'status' => ['prohibited'],
            'started_at' => ['prohibited'],
            'completed_at' => ['prohibited'],

            'notes' => ['sometimes', 'array', 'max:50'],

            'notes.*' => ['array:id,body'],

            'notes.*.id' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'distinct',
            ],

            'notes.*.body' => [
                'required',
                'string',
                'max:10000',
            ],

            'reference_sources' => ['sometimes', 'array', 'max:50'],

            'reference_sources.*' => [
                'array:id,source_name,url,citation',
            ],

            'reference_sources.*.id' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'distinct',
            ],

            'reference_sources.*.source_name' => [
                'required',
                'string',
                'max:255',
            ],

            'reference_sources.*.url' => [
                'nullable',
                'string',
                'url:http,https',
                'max:2048',
            ],

            'reference_sources.*.citation' => [
                'nullable',
                'string',
                'max:10000',
            ],
    
            
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

            'body.required' =>
                'Vui lòng nhập nội dung bài lập luận.',
            'body.string' =>
                'Bài lập luận phải là chuỗi ký tự.',
            'body.max' =>
                'Bài lập luận không được vượt quá 50.000 ký tự.',

            'user_id.prohibited' =>
                'Không được thay đổi tài khoản sở hữu phiên.',
            'practice_content_id.prohibited' =>
                'Không được thay đổi nội dung nguồn của phiên.',
            'status.prohibited' =>
                'Không được thay đổi trạng thái qua chức năng lưu.',
            'started_at.prohibited' =>
                'Không được thay đổi thời điểm bắt đầu.',
            'completed_at.prohibited' =>
                'Không được thay đổi thời điểm hoàn thành.',


            'notes.array' => 'Danh sách ghi chú không hợp lệ.',
            'notes.max' => 'Mỗi phiên được lưu tối đa 50 ghi chú.',
            'notes.*.array' => 'Thông tin ghi chú không hợp lệ.',
            'notes.*.id.required' => 'ID ghi chú không được để trống.',
            'notes.*.id.integer' => 'ID ghi chú phải là số nguyên.',
            'notes.*.id.min' => 'ID ghi chú không hợp lệ.',
            'notes.*.id.distinct' => 'Danh sách có ID ghi chú bị trùng.',
            'notes.*.body.required' => 'Nội dung ghi chú không được để trống.',
            'notes.*.body.string' => 'Nội dung ghi chú phải là chuỗi ký tự.',
            'notes.*.body.max' => 'Mỗi ghi chú không được vượt quá 10.000 ký tự.',   


            'reference_sources.array' =>
            'Danh sách tài liệu tham khảo không hợp lệ.',
            'reference_sources.max' =>
                'Mỗi phiên được lưu tối đa 50 nguồn tham khảo.',
            'reference_sources.*.array' =>
                'Thông tin nguồn tham khảo không hợp lệ.',

            'reference_sources.*.id.required' =>
                'ID nguồn tham khảo không được để trống.',
            'reference_sources.*.id.integer' =>
                'ID nguồn tham khảo phải là số nguyên.',
            'reference_sources.*.id.min' =>
                'ID nguồn tham khảo không hợp lệ.',
            'reference_sources.*.id.distinct' =>
                'Danh sách có ID nguồn tham khảo bị trùng.',

            'reference_sources.*.source_name.required' =>
                'Vui lòng nhập tên nguồn tham khảo.',
            'reference_sources.*.source_name.string' =>
                'Tên nguồn tham khảo phải là chuỗi ký tự.',
            'reference_sources.*.source_name.max' =>
                'Tên nguồn không được vượt quá 255 ký tự.',

            'reference_sources.*.url.string' =>
                'Đường dẫn phải là chuỗi ký tự.',
            'reference_sources.*.url.url' =>
                'Đường dẫn phải hợp lệ và sử dụng HTTP hoặc HTTPS.',
            'reference_sources.*.url.max' =>
                'Đường dẫn không được vượt quá 2.048 ký tự.',

            'reference_sources.*.citation.string' =>
                'Thông tin trích dẫn phải là chuỗi ký tự.',
            'reference_sources.*.citation.max' =>
                'Thông tin trích dẫn không được vượt quá 10.000 ký tự.',
        ];
    }
}