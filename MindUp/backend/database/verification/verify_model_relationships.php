<?php

use App\Models\AIFeedback;
use App\Models\Argument;
use App\Models\ArgumentVersion;
use App\Models\AudioRecording;
use App\Models\PracticeContent;
use App\Models\PracticeSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

(function (): void {
    if (! app()->environment(['local', 'testing'])) {
        throw new RuntimeException(
            'Chỉ chạy kiểm tra trong môi trường local hoặc testing.'
        );
    }

    $connection = DB::connection();

    if ($connection->transactionLevel() !== 0) {
        throw new RuntimeException(
            'Đang có giao dịch khác. Hãy mở một phiên Tinker mới.'
        );
    }

    $connection->beginTransaction();

    try {
        // 1. Tạo tài khoản thử, không sử dụng tài khoản hiện có.
        $user = new User();
        $user->full_name = 'Kiểm thử quan hệ MindUp';
        $user->email = 'model-check-'.Str::uuid().'@example.invalid';
        $user->password = Str::random(40);
        $user->role = 'user';
        $user->status = 'active';
        $user->save();

        // 2. Tạo nội dung luyện tập.
        $content = new PracticeContent();
        $content->creator()->associate($user);
        $content->title = 'Sinh viên có nên đi làm thêm?';
        $content->description = 'Trình bày quan điểm của bạn.';
        $content->guidance = 'Thời gian học tập; trải nghiệm thực tế';
        $content->scope = 'private';
        $content->status = 'available';
        $content->save();

        // 3. Tạo phiên và ghi nhận nội dung tại thời điểm bắt đầu.
        $session = new PracticeSession();
        $session->user()->associate($user);
        $session->practiceContent()->associate($content);
        $session->content_title_snapshot = $content->title;
        $session->content_description_snapshot = $content->description;
        $session->content_guidance_snapshot = $content->guidance;
        $session->target_duration_seconds = 300;
        $session->status = 'in_progress';
        $session->revision = 0;
        $session->started_at = now();
        $session->save();

        // 4. Tạo bài lập luận hiện tại.
        $argument = new Argument();
        $argument->practiceSession()->associate($session);
        $argument->body = 'Theo tôi, sinh viên có thể đi làm thêm '
            .'nếu cân đối được thời gian học.';
        $argument->save();

        // 5. Tạo thông tin bản ghi âm thử.
        // Không tạo hoặc đọc tệp âm thanh trên ổ đĩa.
        $recording = new AudioRecording();
        $recording->argument()->associate($argument);
        $recording->storage_path = 'verification/not-a-real-file.webm';
        $recording->mime_type = 'audio/webm';
        $recording->size_bytes = 1024;
        $recording->duration_seconds = '12.500';
        $recording->transcription_status = 'completed';
        $recording->transcript = 'Nội dung chuyển lời nói thử nghiệm.';
        $recording->save();

        $argument->currentRecording()->associate($recording);
        $argument->save();

        // 6. Ghi nhận phiên bản bài lập luận.
        $version = new ArgumentVersion();
        $version->argument()->associate($argument);
        $version->version_number = 1;
        $version->body_snapshot = $argument->body;
        $version->audioRecording()->associate($recording);
        $version->save();

        // 7. Tạo ghi chú và nguồn tham khảo qua quan hệ của phiên.
        $note = $session->notes()->create([
            'body' => 'Cần phân tích ảnh hưởng đến thời gian học.',
        ]);

        $source = $session->referenceSources()->create([
            'source_name' => 'Nguồn tham khảo thử nghiệm',
            'url' => 'https://example.com',
            'citation' => 'Chỉ phục vụ kiểm tra dữ liệu.',
        ]);

        // 8. Lưu kết quả AI giả lập, không gọi dịch vụ bên ngoài.
        $feedback = new AIFeedback();
        $feedback->argument()->associate($argument);
        $feedback->input_text = $argument->body;
        $feedback->response_text =
            'Điều kiện nào giúp sinh viên cân đối việc học và làm thêm?';
        $feedback->requested_at = now();
        $feedback->responded_at = now();
        $feedback->save();

        $session->last_saved_at = now();
        $session->revision = 1;
        $session->save();

        // Kiểm tra ngay cả các khóa ngoại đang được hoãn.
        $connection->statement('SET CONSTRAINTS ALL IMMEDIATE');

        // 9. Đọc lại từ cơ sở dữ liệu, không dùng quan hệ đã lưu trong RAM.
        $loaded = PracticeSession::query()
            ->with([
                'user',
                'practiceContent.creator',
                'notes.practiceSession',
                'referenceSources.practiceSession',
                'argument.practiceSession',
                'argument.currentRecording',
                'argument.audioRecordings.argument',
                'argument.versions.audioRecording',
                'argument.versions.argument',
                'argument.aiFeedbacks.argument',
            ])
            ->findOrFail($session->id);

        $checks = [
            'Tài khoản sở hữu phiên' =>
                $loaded->user->is($user),

            'Nội dung và người tạo' =>
                $loaded->practiceContent->is($content)
                && $loaded->practiceContent->creator->is($user),

            'Bài lập luận và phiên' =>
                $loaded->argument->is($argument)
                && $loaded->argument->practiceSession->is($session),

            'Bản ghi âm hiện tại' =>
                $loaded->argument->currentRecording->is($recording),

            'Danh sách bản ghi và bài sở hữu' =>
                $loaded->argument->audioRecordings->count() === 1
                && $loaded->argument->audioRecordings
                    ->first()->argument->is($argument),

            'Phiên bản và bản ghi âm' =>
                $loaded->argument->versions->count() === 1
                && $loaded->argument->versions
                    ->first()->audioRecording->is($recording)
                && $loaded->argument->versions
                    ->first()->argument->is($argument),

            'Ghi chú của phiên' =>
                $loaded->notes->count() === 1
                && $loaded->notes->first()->is($note)
                && $loaded->notes->first()->practiceSession->is($session),

            'Nguồn tham khảo của phiên' =>
                $loaded->referenceSources->count() === 1
                && $loaded->referenceSources->first()->is($source)
                && $loaded->referenceSources
                    ->first()->practiceSession->is($session),

            'Câu hỏi AI và nội dung đã gửi' =>
                $loaded->argument->aiFeedbacks->count() === 1
                && $loaded->argument->aiFeedbacks
                    ->first()->argument->is($argument)
                && $loaded->argument->aiFeedbacks
                    ->first()->input_text === $argument->body,

            'Các quan hệ ngược' =>
                $user->practiceContents()->whereKey($content->id)->exists()
                && $user->practiceSessions()->whereKey($session->id)->exists()
                && $content->practiceSessions()->whereKey($session->id)->exists()
                && $recording->argumentVersions()
                    ->whereKey($version->id)->exists(),
        ];

        foreach ($checks as $label => $passed) {
            if (! $passed) {
                throw new RuntimeException('FAIL: '.$label);
            }

            echo 'PASS: '.$label.PHP_EOL;
        }

        echo 'Đã vượt qua '.count($checks).' kiểm tra.'.PHP_EOL;
    } finally {
        $connection->rollBack();

        echo 'Đã hoàn tác dữ liệu thử.'.PHP_EOL;
    }
})();