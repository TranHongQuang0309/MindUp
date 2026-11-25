<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ArgumentVersion
 */
class ArgumentVersionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'version_number' => $this->version_number,
            'body_snapshot' => $this->body_snapshot,
            'audio_recording_id' => $this->audio_recording_id,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}