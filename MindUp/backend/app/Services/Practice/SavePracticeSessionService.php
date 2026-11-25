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
        int $expectedRevision,
        ?string $body,
        ?array $notes = null,
        ?array $referenceSources = null
    ): PracticeSession {
        return DB::transaction(function () use (
            $user,
            $sessionId,
            $expectedRevision,
            $body,
            $notes,
            $referenceSources
        ): PracticeSession {
            $session = PracticeSession::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->findOrFail($sessionId);

            if ($referenceSources !== null) {
                $this->syncReferenceSources($session, $referenceSources);
            } 
                
            if ($session->revision !== $expectedRevision) {
                abort(
                    409,
                    'Phiên đã được cập nhật từ một yêu cầu khác. '
                    .'Vui lòng tải lại phiên trước khi lưu.'
                );
            }

            $argument = $session->argument()->firstOrFail();

            if ($body !== null && $argument->body !== $body) {
                $argument->body = $body;
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

            if ($notes !== null) {
                $this->syncNotes($session, $notes);
            }

            $session->revision = $session->revision + 1;
            $session->last_saved_at = now();
            $session->save();

            return $session->load(['argument', 'notes','referenceSources']);
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