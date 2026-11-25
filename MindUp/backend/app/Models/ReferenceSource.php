<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['source_name', 'url', 'citation'])]
class ReferenceSource extends Model
{
    /**
     * Phiên luyện tập sử dụng nguồn tham khảo.
     */
    public function practiceSession(): BelongsTo
    {
        return $this->belongsTo(
            PracticeSession::class,
            'practice_session_id'
        );
    }
}