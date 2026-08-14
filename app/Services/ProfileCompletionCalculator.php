<?php

namespace App\Services;

use App\Models\AlumniProfile;

class ProfileCompletionCalculator
{
    protected const WEIGHTED_FIELDS = [
        'student_id', 'department_id', 'degree_id', 'major', 'campus_id',
        'job_title', 'organization', 'industry', 'linkedin_url', 'bio',
    ];

    public function calculate(AlumniProfile $profile): int
    {
        $filled = collect(self::WEIGHTED_FIELDS)->filter(fn ($field) => filled($profile->{$field}))->count();
        $fieldScore = ($filled / count(self::WEIGHTED_FIELDS)) * 80;

        $bonusScore = 0;
        $bonusScore += $profile->skills()->exists() ? 10 : 0;
        $bonusScore += $profile->employments()->exists() ? 10 : 0;

        return (int) round(min(100, $fieldScore + $bonusScore));
    }

    public function refresh(AlumniProfile $profile): void
    {
        $profile->update(['profile_completion' => $this->calculate($profile)]);
    }
}
