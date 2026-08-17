<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MentorshipRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';

    public const ADMIN_STATUS_PENDING = 'pending';
    public const ADMIN_STATUS_APPROVED = 'approved';
    public const ADMIN_STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'mentee_id', 'mentor_id', 'message', 'status', 'responded_at',
        'admin_status', 'admin_reviewed_by', 'admin_reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
            'admin_reviewed_at' => 'datetime',
        ];
    }

    public function mentee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function adminReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_reviewed_by');
    }

    public function mentorship(): HasOne
    {
        return $this->hasOne(Mentorship::class);
    }

    public function isFullyApproved(): bool
    {
        return $this->status === self::STATUS_ACCEPTED && $this->admin_status === self::ADMIN_STATUS_APPROVED;
    }

    /**
     * Once the mentor has accepted AND the admin has approved, create the active Mentorship.
     * Safe to call after either side's decision — only activates when both are in.
     */
    public function activateIfFullyApproved(): void
    {
        if ($this->isFullyApproved() && ! $this->mentorship) {
            $this->mentorship()->create([
                'mentor_id' => $this->mentor_id,
                'mentee_id' => $this->mentee_id,
                'started_at' => now(),
                'status' => Mentorship::STATUS_ACTIVE,
            ]);
        }
    }
}
