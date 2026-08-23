<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarpoolBooking extends Model
{
    use HasFactory;

    public const STATUS_REQUESTED = 'requested';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_REFUNDED = 'refunded';

    public const PAYOUT_PENDING = 'pending';
    public const PAYOUT_PAID = 'paid';

    protected $fillable = [
        'carpool_schedule_id', 'passenger_id', 'seats', 'seat_price_snapshot', 'total_fare',
        'status', 'driver_responded_at', 'payment_deadline_at', 'payment_status',
        'commission_percentage_snapshot', 'commission_amount', 'driver_payout_amount',
        'payout_status', 'paid_out_at', 'paid_out_by', 'cancelled_by', 'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'seat_price_snapshot' => 'decimal:2',
            'total_fare' => 'decimal:2',
            'driver_responded_at' => 'datetime',
            'payment_deadline_at' => 'datetime',
            'commission_percentage_snapshot' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'driver_payout_amount' => 'decimal:2',
            'paid_out_at' => 'datetime',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(CarpoolSchedule::class, 'carpool_schedule_id');
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    public function paidOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_out_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CarpoolPayment::class);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_REQUESTED, self::STATUS_ACCEPTED]);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopePayoutPending($query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID)->where('payout_status', self::PAYOUT_PENDING);
    }
}
