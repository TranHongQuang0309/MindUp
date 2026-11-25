<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'guidance'])]
class PracticeContent extends Model
{
    /**
     * Tài khoản tạo nội dung luyện tập.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Các phiên luyện tập bắt đầu từ nội dung này.
     */
    public function practiceSessions(): HasMany
    {
        return $this->hasMany(
            PracticeSession::class,
            'practice_content_id'
        );
    }

    /**
     * Nội dung đang được cung cấp và tài khoản được phép lựa chọn.
     */
    #[Scope]
    protected function availableTo(
        Builder $query,
        int $userId
    ): void {
        $query->where('status', 'available')
            ->where(function (Builder $query) use ($userId): void {
                $query->where('scope', 'shared')
                    ->orWhere(function (Builder $query) use ($userId): void {
                        $query->where('scope', 'private')
                            ->where('created_by', $userId);
                    });
            });
    }
}