<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketplaceListing extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id', 'marketplace_category_id', 'title', 'slug', 'description',
        'price', 'price_unit', 'address', 'city', 'video_url', 'details',
        'status', 'approved_by', 'approved_at', 'rejection_reason', 'expires_at',
        'is_featured', 'views_count', 'inquiries_count',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'price' => 'decimal:2',
            'approved_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_featured' => 'boolean',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MarketplaceCategory::class, 'marketplace_category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(MarketplaceListingImage::class)->orderBy('sort_order');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(MarketplaceOrder::class);
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

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        $cover = $this->relationLoaded('images') ? $this->images->first() : $this->images()->first();

        return $cover ? asset('storage/' . $cover->path) : null;
    }

    public function getMapEmbedUrlAttribute(): string
    {
        $query = trim($this->address . ($this->city ? ', ' . $this->city : ''));

        return 'https://www.google.com/maps?q=' . urlencode($query) . '&output=embed';
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([\w-]{11})/', $this->video_url, $matches)) {
            return "https://www.youtube.com/embed/{$matches[1]}";
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $this->video_url, $matches)) {
            return "https://player.vimeo.com/video/{$matches[1]}";
        }

        return null;
    }
}
