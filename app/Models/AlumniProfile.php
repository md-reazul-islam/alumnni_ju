<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumniProfile extends Model
{
    use HasFactory, SoftDeletes;

    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_ALUMNI = 'alumni';
    public const VISIBILITY_PRIVATE = 'private';

    protected $fillable = [
        'user_id', 'student_id', 'department_id', 'program_id', 'degree_id', 'campus_id',
        'major', 'admission_year', 'graduation_year', 'batch',
        'date_of_birth', 'gender', 'country', 'city', 'address', 'cover_image',
        'job_title', 'organization', 'industry', 'employment_type', 'work_location',
        'linkedin_url', 'website_url', 'bio', 'profile_visibility', 'profile_completion',
        'verified_by', 'verified_at', 'tags',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function degree(): BelongsTo
    {
        return $this->belongsTo(Degree::class);
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    public function employments(): HasMany
    {
        return $this->hasMany(Employment::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'alumni_profile_skill');
    }

    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class, 'alumni_profile_interest');
    }

    public function stories(): HasMany
    {
        return $this->hasMany(AlumniStory::class);
    }

    // Accessors

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }

    public function isVerified(): bool
    {
        return ! is_null($this->verified_at);
    }

    // Scopes

    public function scopePubliclyVisible($query)
    {
        return $query->where('profile_visibility', self::VISIBILITY_PUBLIC);
    }

    public function scopeVisibleToAlumni($query)
    {
        return $query->whereIn('profile_visibility', [self::VISIBILITY_PUBLIC, self::VISIBILITY_ALUMNI]);
    }
}
