<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArgumentVersion extends Model
{
    
    public const UPDATED_AT = null;

    /**
     * Dữ liệu phiên bản do Backend xác lập tại thời điểm lưu.
     *
     * @var array<string>
     */
    protected $guarded = ['*'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'version_number' => 'integer',
        ];
    }

    public function argument(): BelongsTo
    {
        return $this->belongsTo(
            Argument::class,
            'argument_id'
        );
    }

    
    public function audioRecording(): BelongsTo
    {
        return $this->belongsTo(
            AudioRecording::class,
            'audio_recording_id'
        );
    }
}