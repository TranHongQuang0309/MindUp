<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AudioRecording extends Model
{
    /**
     * Thông tin tệp và kết quả chuyển lời nói thành văn bản
     * được Backend gán rõ ràng.
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
            'size_bytes' => 'integer',
            'duration_seconds' => 'decimal:3',
        ];
    }

    /**
     * Bài lập luận sở hữu bản ghi âm.
     */
    public function argument(): BelongsTo
    {
        return $this->belongsTo(
            Argument::class,
            'argument_id'
        );
    }
    public function argumentVersions(): HasMany
    {
    return $this->hasMany(
        ArgumentVersion::class,
        'audio_recording_id'
    );
    }
    
}