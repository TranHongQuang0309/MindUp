<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIFeedback extends Model
{
    /**
     * Tên bảng được khai báo rõ để tránh suy diễn
     * không thống nhất đối với tên viết tắt AI.
     *
     * @var string
     */
    protected $table = 'ai_feedbacks';

    /**
     * Bảng sử dụng requested_at và responded_at,
     * không có created_at và updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Nội dung yêu cầu và kết quả AI do Backend gán.
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
            'requested_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    /**
     * Bài lập luận liên quan đến lần yêu cầu AI.
     */
    public function argument(): BelongsTo
    {
        return $this->belongsTo(
            Argument::class,
            'argument_id'
        );
    }
}