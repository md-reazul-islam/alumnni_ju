<?php

namespace App\Services;

use App\Models\MatrimonyProfile;

class MatrimonyProfileCompletionCalculator
{
    protected const WEIGHTED_FIELDS = [
        'full_name', 'display_name', 'date_of_birth', 'height_cm', 'marital_status',
        'religion', 'nationality', 'country', 'city', 'education_level', 'occupation',
        'about_me', 'contact_phone', 'contact_email',
        'preferred_age_min', 'preferred_age_max', 'preferred_partner_details',
    ];

    public function calculate(MatrimonyProfile $profile): int
    {
        $filled = collect(self::WEIGHTED_FIELDS)->filter(fn ($field) => filled($profile->{$field}))->count();
        $fieldScore = ($filled / count(self::WEIGHTED_FIELDS)) * 90;

        $bonusScore = $profile->photos()->exists() ? 10 : 0;

        return (int) round(min(100, $fieldScore + $bonusScore));
    }

    public function refresh(MatrimonyProfile $profile): void
    {
        $profile->update(['profile_completion' => $this->calculate($profile)]);
    }
}
