<?php

namespace App\Services\Practice;

use App\Models\Argument;
use App\Models\PracticeContent;
use App\Models\PracticeSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StartPracticeSessionService
{
    public function start(
        User $user,
        int $contentId,
        ?int $targetDurationSeconds
    ): PracticeSession {
        return DB::transaction(function () use (
            $user,
            $contentId,
            $targetDurationSeconds
        ): PracticeSession {
            $content = PracticeContent::query()
                ->availableTo($user->id)
                ->sharedLock()
                ->findOrFail($contentId);

            $session = new PracticeSession();
            $session->user()->associate($user);
            $session->practiceContent()->associate($content);

            $session->content_title_snapshot = $content->title;
            $session->content_description_snapshot = $content->description;
            $session->content_guidance_snapshot = $content->guidance;

            $session->status = 'in_progress';
            $session->revision = 0;
            $session->target_duration_seconds = $targetDurationSeconds;
            $session->started_at = now();
            $session->last_saved_at = null;
            $session->completed_at = null;
            $session->save();

            $argument = new Argument();
            $argument->practiceSession()->associate($session);
            $argument->body = null;
            $argument->current_recording_id = null;
            $argument->save();

            return $session->load('argument');
        });
    }
}