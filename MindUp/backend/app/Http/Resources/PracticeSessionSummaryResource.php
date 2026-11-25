<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\PracticeSession
 */
class PracticeSessionSummaryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'practice_content_id' => $this->practice_content_id,
            'content_title_snapshot' => $this->content_title_snapshot,
            'status' => $this->status,
            'revision' => $this->revision,
            'target_duration_seconds' => $this->target_duration_seconds,
            'started_at' => $this->started_at->toISOString(),
            'last_saved_at' => $this->last_saved_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}