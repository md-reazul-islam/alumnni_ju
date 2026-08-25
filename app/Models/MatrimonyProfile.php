<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatrimonyProfile extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'created_by', 'managed_by_relation',
        'full_name', 'display_name', 'gender', 'date_of_birth', 'height_cm', 'marital_status',
        'religion', 'sect', 'mother_tongue', 'nationality', 'country', 'state', 'city', 'visa_status',
        'education_level', 'education_details', 'occupation', 'occupation_details', 'about_me',
        'income_range', 'physical_description', 'family_details',
        'guardian_name', 'guardian_phone', 'guardian_email', 'contact_phone', 'contact_email',
        'preferred_age_min', 'preferred_age_max', 'preferred_country', 'preferred_partner_details',
        'photo_visibility', 'status', 'is_active', 'is_verified', 'rejection_reason',
        'reviewed_by', 'reviewed_at', 'terms_accepted_at', 'profile_completion', 'views_count',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'reviewed_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(MatrimonyProfilePhoto::class)->orderBy('sort_order');
    }

    public function interests(): HasMany
    {
        return $this->hasMany(MatrimonyInterest::class);
    }

    public function favoritedBy(): HasMany
    {
        return $this->hasMany(MatrimonyFavorite::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(MatrimonyProfileView::class);
    }

    public function getAgeAttribute(): int
    {
        return (int) $this->date_of_birth->diffInYears(now());
    }

    public function getPrimaryPhotoAttribute(): ?MatrimonyProfilePhoto
    {
        return $this->photos->firstWhere('is_primary', true) ?? $this->photos->first();
    }

    /**
     * Whether $viewer may see this profile's private-tier fields: the owner, an admin,
     * or a user with an accepted MatrimonyInterest connecting them to this profile's owner.
     */
    public function isFullyVisibleTo(?User $viewer): bool
    {
        if (! $viewer) {
            return false;
        }

        if ($viewer->id === $this->created_by) {
            return true;
        }

        if ($viewer->hasPermission('manage-matrimony')) {
            return true;
        }

        // $viewer directly sent an accepted interest to this exact profile.
        $viewerRequestedThis = $this->interests()
            ->where('status', MatrimonyInterest::STATUS_ACCEPTED)
            ->where('requested_by', $viewer->id)
            ->exists();

        if ($viewerRequestedThis) {
            return true;
        }

        // This profile's owner sent an accepted interest to a profile $viewer owns —
        // the connection is between the two people, regardless of which of their
        // (possibly several) profiles was the specific target.
        return MatrimonyInterest::where('requested_by', $this->created_by)
            ->where('status', MatrimonyInterest::STATUS_ACCEPTED)
            ->whereHas('profile', fn ($q) => $q->where('created_by', $viewer->id))
            ->exists();
    }

    public function photoVisibleTo(?User $viewer): bool
    {
        return $this->photo_visibility === 'public' || $this->isFullyVisibleTo($viewer);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
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

    public function scopeSuspended($query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    public function scopeSearchable($query)
    {
        return $query->where('status', self::STATUS_APPROVED)->where('is_active', true);
    }
}
