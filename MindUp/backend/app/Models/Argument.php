<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['body'])]
class Argument extends Model
{
    
    public function practiceSession(): BelongsTo
    {
        return $this->belongsTo(
            PracticeSession::class,
            'practice_session_id'
        );
    }

    public function audioRecordings(): HasMany
    {
    return $this->hasMany(
        AudioRecording::class,
        'argument_id'
    );
    }


    public function currentRecording(): BelongsTo
    {   
    return $this->belongsTo(
        AudioRecording::class,
        'current_recording_id'
    );
    }

    public function versions(): HasMany
    {
    return $this->hasMany(
        ArgumentVersion::class,
        'argument_id'
    );
    }
}