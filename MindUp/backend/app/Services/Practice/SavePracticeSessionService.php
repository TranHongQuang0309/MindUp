<?php

namespace App\Services\Practice;

use App\Models\ArgumentVersion;
use App\Models\Note;
use App\Models\PracticeSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\ReferenceSource;

class SavePracticeSessionService
{
    public function save(
    User $user,
    int $sessionId,
    array $changes
): PracticeSession {
    return DB::transaction(function () use (
        $user,
        $sessionId,
        $changes
    ): PracticeSession {
        $session = PracticeSession::query()
            ->where('user_id', $user->id)
            ->lockForUpdate()
            ->findOrFail($sessionId);

        if ($session->revision !== (int) $changes['revision']) {
            abort(
                409,
                'Phiên đã được cập nhật từ một yêu cầu khác. '
                .'Vui lòng tải lại phiên trước khi lưu.'
            );
        }

        $argument = $session->argument()->firstOrFail();

        if (array_key_exists('body', $changes)) {
            $argument->body = $changes['body'];
        }

        if (array_key_exists('current_recording_id', $changes)) {
            $recordingId = $changes['current_recording_id'];

            if ($recordingId === null) {
                $argument->currentRecording()->dissociate();
            } else {
                $recording = $argument->audioRecordings()
                    ->whereKey((int) $recordingId)
                    ->first();

                if (! $recording) {
                    throw ValidationException::withMessages([
                        'current_recording_id' =>
                            'Bản ghi âm không tồn tại trong bài lập luận này.',
                    ]);
                }

                $argument->currentRecording()->associate($recording);
            }
        }

        if ($argument->isDirty(['body', 'current_recording_id'])) {
            $hasText = trim((string) $argument->body) !== '';
            $hasRecording = $argument->current_recording_id !== null;

            if (! $hasText && ! $hasRecording) {
                throw ValidationException::withMessages([
                    'body' => 'Bài lập luận cần có văn bản '
                        .'hoặc một bản ghi âm.',
                ]);
            }

            $argument->save();

            $versionNumber = (
                (int) $argument->versions()->max('version_number')
            ) + 1;

            $version = new ArgumentVersion();
            $version->argument()->associate($argument);
            $version->version_number = $versionNumber;
            $version->body_snapshot = $argument->body;
            $version->audio_recording_id =
                $argument->current_recording_id;
            $version->save();
        }

        if (array_key_exists('notes', $changes)) {
            $this->syncNotes($session, $changes['notes']);
        }

        if (array_key_exists('reference_sources', $changes)) {
            $this->syncReferenceSources(
                $session,
                $changes['reference_sources']
            );
        }

        $session->revision = $session->revision + 1;
        $session->last_saved_at = now();
        $session->save();

        return $session->load([
            'argument',
            'notes',
            'referenceSources',
        ]);
    });
}

    private function syncNotes(
        PracticeSession $session,
        array $notes
    ): void {
        $existingNotes = $session->notes()->get()->keyBy('id');
        $keptIds = [];

        foreach ($notes as $index => $item) {
            if (isset($item['id'])) {
                $note = $existingNotes->get($item['id']);

                if (! $note) {
                    throw ValidationException::withMessages([
                        "notes.$index.id" =>
                            'Ghi chú không tồn tại trong phiên này.',
                    ]);
                }
            } else {
                $note = new Note();
                $note->practiceSession()->associate($session);
            }

            $note->body = $item['body'];

            if (! $note->exists || $note->isDirty('body')) {
                $note->save();
            }

            $keptIds[] = $note->id;
        }

        $session->notes()
            ->whereNotIn('id', $keptIds)
            ->delete();
    }

    private function syncReferenceSources(
        PracticeSession $session,
        array $sources
    ): void {
        $existingSources = $session->referenceSources()
            ->get()
            ->keyBy('id');

        $keptIds = [];

        foreach ($sources as $index => $item) {
            if (isset($item['id'])) {
                $source = $existingSources->get($item['id']);

                if (! $source) {
                    throw ValidationException::withMessages([
                        "reference_sources.$index.id" =>
                            'Nguồn tham khảo không tồn tại trong phiên này.',
                    ]);
                }
            } else {
                $source = new ReferenceSource();
                $source->practiceSession()->associate($session);
            }

            $source->source_name = $item['source_name'];
            $source->url = $item['url'] ?? null;
            $source->citation = $item['citation'] ?? null;

            if (! $source->exists || $source->isDirty()) {
                $source->save();
            }

            $keptIds[] = $source->id;
        }

        $session->referenceSources()
            ->whereNotIn('id', $keptIds)
            ->delete();
    }
}