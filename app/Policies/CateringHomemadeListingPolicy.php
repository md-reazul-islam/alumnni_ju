<?php

namespace App\Policies;

use App\Models\CateringHomemadeListing;
use App\Models\User;

class CateringHomemadeListingPolicy
{
    public function create(User $user): bool
    {
        return $user->isVerified();
    }

    public function update(User $user, CateringHomemadeListing $listing): bool
    {
        return $user->id === $listing->user_id || $user->hasPermission('manage-catering');
    }

    public function delete(User $user, CateringHomemadeListing $listing): bool
    {
        return $user->id === $listing->user_id || $user->hasPermission('manage-catering');
    }

    public function approve(User $user, CateringHomemadeListing $listing): bool
    {
        return $user->hasPermission('manage-catering');
    }
}
