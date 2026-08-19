<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BorrowRequest extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_HANDED_OVER = 'handed_over';
    public const STATUS_RETURNED = 'returned';

    protected $fillable = [
        'book_id', 'user_id', 'duration_months', 'status', 'reviewed_by', 'reviewed_at',
        'handed_over_at', 'due_date', 'returned_at', 'last_reminder_sent_at', 'overdue_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'handed_over_at' => 'datetime',
            'due_date' => 'date',
            'returned_at' => 'datetime',
            'last_reminder_sent_at' => 'datetime',
            'overdue_notified_at' => 'datetime',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_HANDED_OVER && $this->due_date && $this->due_date->isPast();
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeHandedOver($query)
    {
        return $query->where('status', self::STATUS_HANDED_OVER);
    }
}
