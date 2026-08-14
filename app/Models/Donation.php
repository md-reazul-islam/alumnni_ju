<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_COMPLETED = 'completed';
    public const PAYMENT_FAILED = 'failed';
    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'donor_id', 'donation_campaign_id', 'donor_name', 'donor_email', 'amount', 'currency',
        'category', 'is_anonymous', 'payment_method', 'payment_status', 'transaction_reference', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_anonymous' => 'boolean',
        ];
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(DonationCampaign::class, 'donation_campaign_id');
    }

    public function displayName(): string
    {
        return $this->is_anonymous ? 'Anonymous' : ($this->donor?->full_name ?? $this->donor_name ?? 'Anonymous');
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', self::PAYMENT_COMPLETED);
    }
}
