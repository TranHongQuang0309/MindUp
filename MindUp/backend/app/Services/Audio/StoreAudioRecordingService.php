<?php

namespace App\Services\Audio;

use App\Models\AudioRecording;
use App\Models\PracticeSession;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class StoreAudioRecordingService
{
    public function __construct(
        private InspectAudioFileService $inspector
    ) {
    }

    public function store(
        User $user,
        int $sessionId,
        UploadedFile $file
    ): AudioRecording {
        // Kiểm tra quyền trước khi phân tích và lưu tệp.
        PracticeSession::query()
            ->where('user_id', $user->id)
            ->findOrFail($sessionId);

        $temporaryPath = $file->getRealPath();

        if (! $file->isValid() || $temporaryPath === false) {
            throw ValidationException::withMessages([
                'audio' => 'Tệp tải lên không hợp lệ.',
            ]);
        }

        $metadata = $this->inspector->inspect($temporaryPath);

        $diskName = config('media.audio_disk', 'local');

        if ($diskName !== 'local') {
            throw new RuntimeException(
                'Luồng âm thanh hiện tại yêu cầu sử dụng disk local.'
            );
        }

        $disk = Storage::disk($diskName);

        $directory = 'practice-audio/'
            .$user->id.'/'
            .$sessionId;

        $filename = Str::uuid()->toString()
            .'.'.$metadata['extension'];

        $path = $directory.'/'.$filename;

        try {
            $storedPath = $disk->putFileAs(
                $directory,
                $file,
                $filename
            );

            if ($storedPath === false) {
                throw new RuntimeException(
                    'Không thể lưu tệp âm thanh.'
                );
            }

            $sizeBytes = $disk->size($storedPath);

            return DB::transaction(function () use (
                $user,
                $sessionId,
                $storedPath,
                $sizeBytes,
                $metadata
            ): AudioRecording {
                // Kiểm tra lại sau khi xử lý tệp và khóa phiên.
                // Phiên có thể đã bị xóa trong thời gian tải lên.
                $session = PracticeSession::query()
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->findOrFail($sessionId);

                $argument = $session->argument()->firstOrFail();

                $recording = new AudioRecording();
                $recording->argument()->associate($argument);
                $recording->storage_path = $storedPath;
                $recording->mime_type = $metadata['mime_type'];
                $recording->size_bytes = $sizeBytes;
                $recording->duration_seconds =
                    $metadata['duration_seconds'];
                $recording->transcription_status = 'pending';
                $recording->transcript = null;
                $recording->transcription_error = null;
                $recording->save();

                return $recording;
            });
        } catch (Throwable $exception) {
            try {
                if (! $disk->delete($path)) {
                    throw new RuntimeException(
                        'Không thể dọn tệp sau khi lưu thất bại.'
                    );
                }
            } catch (Throwable $cleanupException) {
                Log::error('Không thể dọn tệp âm thanh tải lên thất bại.', [
                    'path' => $path,
                    'error' => $cleanupException->getMessage(),
                ]);
            }

            throw $exception;
        }
    }
}