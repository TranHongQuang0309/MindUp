<?php

namespace App\Http\Requests\PracticeSession;

use Illuminate\Foundation\Http\FormRequest;

class StoreAudioRecordingRequest extends FormRequest
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
            'audio' => [
                'bail',
                'required',
                'file',
                'max:25600',
                'mimetypes:audio/mpeg,audio/mp3,audio/wav,'
                    .'audio/x-wav,audio/vnd.wave,audio/ogg,'
                    .'application/ogg,audio/webm,video/webm,'
                    .'audio/mp4,video/mp4,audio/x-m4a',
            ],

            'argument_id' => ['prohibited'],
            'storage_path' => ['prohibited'],
            'transcription_status' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'audio.required' => 'Vui lòng cung cấp tệp âm thanh.',
            'audio.file' => 'Dữ liệu tải lên phải là một tệp.',
            'audio.uploaded' =>
                'Không thể nhận tệp. Vui lòng kiểm tra dung lượng và thử lại.',
            'audio.max' => 'Tệp âm thanh không được vượt quá 25 MiB.',
            'audio.mimetypes' =>
                'Định dạng tệp chưa được hỗ trợ. '
                .'Vui lòng dùng MP3, WAV, OGG, WebM hoặc M4A/MP4.',

            'argument_id.prohibited' =>
                'Không được tự chỉ định bài lập luận.',
            'storage_path.prohibited' =>
                'Không được tự chỉ định đường dẫn lưu tệp.',
            'transcription_status.prohibited' =>
                'Không được tự chỉ định trạng thái chuyển lời nói thành văn bản.',
        ];
    }
}