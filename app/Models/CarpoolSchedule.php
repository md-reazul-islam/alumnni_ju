<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarpoolSchedule extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'carpool_driver_profile_id', 'carpool_car_id', 'origin', 'destination',
        'departure_date', 'departure_time', 'price_per_seat', 'seats_offered', 'seats_booked',
        'status', 'approved_by', 'approved_at', 'rejection_reason', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'price_per_seat' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function driverProfile(): BelongsTo
    {
        return $this->belongsTo(CarpoolDriverProfile::class, 'carpool_driver_profile_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(CarpoolCar::class, 'carpool_car_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(CarpoolBooking::class);
    }

    /**
     * Seats currently held by requests the driver has accepted but the passenger hasn't paid for yet.
     * Must be counted alongside seats_booked whenever checking remaining capacity, since seats_booked
     * only increments on payment success (see CarpoolBookingController).
     */
    public function heldSeats(?int $excludingBookingId = null): int
    {
        return $this->bookings()
            ->where('status', CarpoolBooking::STATUS_ACCEPTED)
            ->when($excludingBookingId, fn ($q) => $q->where('id', '!=', $excludingBookingId))
            ->sum('seats');
    }

    public function seatsRemaining(?int $excludingBookingId = null): int
    {
        return $this->seats_offered - $this->seats_booked - $this->heldSeats($excludingBookingId);
    }

    public function isLocked(): bool
    {
        return $this->seats_booked > 0;
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

    public function scopeUpcoming($query)
    {
        return $query->where('departure_date', '>=', today());
    }
}
