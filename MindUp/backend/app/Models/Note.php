<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['body'])]
class Note extends Model
{
    /**
     * Phiên luyện tập chứa ghi chú.
     */
    public function practiceSession(): BelongsTo
    {
        return $this->belongsTo(
            PracticeSession::class,
            'practice_session_id'
        );
    }
}