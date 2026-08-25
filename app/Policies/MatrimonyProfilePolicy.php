<?php

namespace App\Policies;

use App\Models\MatrimonyProfile;
use App\Models\User;

class MatrimonyProfilePolicy
{
    public function update(User $user, MatrimonyProfile $profile): bool
    {
        return $user->id === $profile->created_by;
    }

    public function delete(User $user, MatrimonyProfile $profile): bool
    {
        return $user->id === $profile->created_by;
    }
}
