<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
class PracticeSession extends Model
{
    /**
     *
     * @var array<string>
     */
    protected $guarded = ['*'];

    /**
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'revision' => 'integer',
            'target_duration_seconds' => 'integer',
            'started_at' => 'datetime',
            'last_saved_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    
    public function practiceContent(): BelongsTo
    {
        return $this->belongsTo(
            PracticeContent::class,
            'practice_content_id'
        );
    }

    public function argument(): HasOne
    {
    return $this->hasOne(
        Argument::class,
        'practice_session_id'
    );
    }
}