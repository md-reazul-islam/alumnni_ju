<?php

namespace App\Policies;

use App\Models\CarpoolCar;
use App\Models\User;

class CarpoolCarPolicy
{
    public function create(User $user): bool
    {
        return $user->isApprovedCarpoolDriver();
    }

    public function update(User $user, CarpoolCar $car): bool
    {
        return $user->id === $car->driverProfile->user_id;
    }

    public function delete(User $user, CarpoolCar $car): bool
    {
        return $user->id === $car->driverProfile->user_id;
    }
}
