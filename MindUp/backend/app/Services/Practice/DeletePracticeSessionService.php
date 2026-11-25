<?php

namespace App\Services\Practice;

use App\Jobs\DeletePracticeSessionAudioFiles;
use App\Models\PracticeSession;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DeletePracticeSessionService
{
    public function delete(
        User $user,
        int $sessionId,
        int $expectedRevision
    ): void {
        DB::transaction(function () use (
            $user,
            $sessionId,
            $expectedRevision
        ): void {
            $session = PracticeSession::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->findOrFail($sessionId);

            if ($session->revision !== $expectedRevision) {
                abort(
                    409,
                    'Phiên đã được cập nhật từ một yêu cầu khác. '
                    .'Vui lòng tải lại trước khi quyết định xóa.'
                );
            }

            $argument = $session->argument()->first();

            $paths = $argument
                ? $argument->audioRecordings()
                    ->pluck('storage_path')
                    ->unique()
                    ->values()
                    ->all()
                : [];

            DB::statement(
                'SET CONSTRAINTS '
                .'arguments_current_recording_owner_fk, '
                .'argument_versions_recording_owner_fk '
                .'DEFERRED'
            );

            $session->delete();

            if ($paths !== []) {
                $this->enqueueFileCleanup($paths);
            }
        });
    }

    /**
     * @param array<int, string> $paths
     */
    private function enqueueFileCleanup(array $paths): void
    {
        $queueConnection = config(
            'queue.connections.database.connection'
        ) ?? config('database.default');

        if (
            config('queue.connections.database.driver') !== 'database'
            || $queueConnection !== config('database.default')
        ) {
            throw new RuntimeException(
                'Hàng đợi dọn tệp phải dùng cùng kết nối '
                .'cơ sở dữ liệu với phiên luyện tập.'
            );
        }

        Bus::dispatch(
            (new DeletePracticeSessionAudioFiles($paths))
                ->onConnection('database')
                ->onQueue('file-cleanup')
                ->beforeCommit()
        );
    }
}