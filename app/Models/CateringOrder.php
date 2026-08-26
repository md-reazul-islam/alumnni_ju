<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CateringOrder extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_PRICED = 'priced';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'customer_id', 'catering_program_category_id', 'event_date', 'guest_count',
        'delivery_address', 'contact_phone', 'notes', 'status',
        'subtotal', 'tax_percentage_snapshot', 'tax_amount',
        'vat_percentage_snapshot', 'vat_amount',
        'service_fee_percentage_snapshot', 'service_fee_amount', 'total_amount',
        'priced_by', 'priced_at', 'customer_responded_at', 'rejection_reason', 'payment_status',
        'delivered_by', 'delivered_at', 'cancelled_by', 'cancellation_reason', 'cancelled_at',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_percentage_snapshot' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'vat_percentage_snapshot' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'service_fee_percentage_snapshot' => 'decimal:2',
            'service_fee_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'priced_at' => 'datetime',
            'customer_responded_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CateringProgramCategory::class, 'catering_program_category_id');
    }

    public function pricer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'priced_by');
    }

    public function deliverer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CateringOrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CateringPayment::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(CateringOrderFeedback::class);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
