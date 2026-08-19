<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = ['donor_id', 'title', 'author', 'cover', 'description', 'status', 'approved_by', 'approved_at'];

    protected function casts(): array
    {
        return ['approved_at' => 'datetime'];
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function borrowRequests(): HasMany
    {
        return $this->hasMany(BorrowRequest::class);
    }

    public function activeBorrow(): ?BorrowRequest
    {
        return $this->borrowRequests->firstWhere(fn ($request) => in_array($request->status, [BorrowRequest::STATUS_APPROVED, BorrowRequest::STATUS_HANDED_OVER], true));
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover ? asset('storage/' . $this->cover) : null;
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_APPROVED)
            ->whereDoesntHave('borrowRequests', fn ($q) => $q->whereIn('status', [BorrowRequest::STATUS_APPROVED, BorrowRequest::STATUS_HANDED_OVER]));
    }
}
