<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['body'])]
class Argument extends Model
{
    /**
     * Phiên luyện tập chứa bài lập luận.
     */
    public function practiceSession(): BelongsTo
    {
        return $this->belongsTo(
            PracticeSession::class,
            'practice_session_id'
        );
    }
}