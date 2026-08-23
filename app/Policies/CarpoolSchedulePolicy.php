<?php

namespace App\Policies;

use App\Models\CarpoolSchedule;
use App\Models\User;

class CarpoolSchedulePolicy
{
    public function create(User $user): bool
    {
        return $user->isApprovedCarpoolDriver();
    }

    public function update(User $user, CarpoolSchedule $schedule): bool
    {
        return $user->id === $schedule->driverProfile->user_id;
    }
}
