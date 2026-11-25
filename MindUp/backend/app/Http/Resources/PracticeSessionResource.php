<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\PracticeSession
 */
class PracticeSessionResource extends JsonResource
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
            'content_description_snapshot' =>
                $this->content_description_snapshot,
            'content_guidance_snapshot' =>
                $this->content_guidance_snapshot,

            'status' => $this->status,
            'revision' => $this->revision,
            'target_duration_seconds' => $this->target_duration_seconds,

            'started_at' => $this->started_at->toISOString(),
            'last_saved_at' => $this->last_saved_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            'argument' => $this->whenLoaded('argument', function (): array {
                return [
                    'id' => $this->argument->id,
                    'body' => $this->argument->body,
                    'current_recording_id' =>
                        $this->argument->current_recording_id,
                ];
            }),

            'notes' => $this->whenLoaded('notes', function (): array {
                return $this->notes->map(function ($note): array {
                    return [
                        'id' => $note->id,
                        'body' => $note->body,
                        'created_at' => $note->created_at->toISOString(),
                        'updated_at' => $note->updated_at->toISOString(),
                    ];
                })->all();
            }),

            'reference_sources' => $this->whenLoaded(
                'referenceSources',
                function (): array {
                    return $this->referenceSources->map(function ($source): array {
                        return [
                            'id' => $source->id,
                            'source_name' => $source->source_name,
                            'url' => $source->url,
                            'citation' => $source->citation,
                            'created_at' => $source->created_at->toISOString(),
                            'updated_at' => $source->updated_at->toISOString(),
                        ];
                    })->all();
                }
            ),

        ];
    }
}