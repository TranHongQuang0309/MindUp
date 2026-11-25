<?php

namespace App\Services\Practice;

use App\Models\PracticeSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompletePracticeSessionService
{
    public function complete(
        User $user,
        int $sessionId,
        int $expectedRevision
    ): PracticeSession {
        return DB::transaction(function () use (
            $user,
            $sessionId,
            $expectedRevision
        ): PracticeSession {
            $session = PracticeSession::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->findOrFail($sessionId);

            if ($session->revision !== $expectedRevision) {
                abort(
                    409,
                    'Phiên đã được cập nhật từ một yêu cầu khác. '
                    .'Vui lòng tải lại phiên trước khi hoàn thành.'
                );
            }

            if ($session->status === 'completed') {
                return $session->load([
                    'argument',
                    'notes',
                    'referenceSources',
                ]);
            }

            $argument = $session->argument()->firstOrFail();

            $hasText = trim((string) $argument->body) !== '';
            $hasRecording = $argument->currentRecording()->exists();

            if (
                $session->last_saved_at === null
                || (! $hasText && ! $hasRecording)
            ) {
                throw ValidationException::withMessages([
                    'session' => 'Cần lưu bài lập luận có nội dung '
                        .'trước khi hoàn thành phiên.',
                ]);
            }

            $session->status = 'completed';
            $session->completed_at = now();
            $session->revision = $session->revision + 1;
            $session->save();

            return $session->load([
                'argument',
                'notes',
                'referenceSources',
            ]);
        });
    }
}