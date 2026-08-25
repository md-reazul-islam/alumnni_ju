<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatrimonyInterest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_WITHDRAWN = 'withdrawn';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'matrimony_profile_id', 'requested_by', 'requester_profile_id',
        'status', 'message', 'responded_at', 'responded_by', 'conversation_id',
    ];

    protected function casts(): array
    {
        return ['responded_at' => 'datetime'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(MatrimonyProfile::class, 'matrimony_profile_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function requesterProfile(): BelongsTo
    {
        return $this->belongsTo(MatrimonyProfile::class, 'requester_profile_id');
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }
}
