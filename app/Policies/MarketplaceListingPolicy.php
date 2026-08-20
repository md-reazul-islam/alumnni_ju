<?php

namespace App\Policies;

use App\Models\MarketplaceListing;
use App\Models\User;

class MarketplaceListingPolicy
{
    public function create(User $user): bool
    {
        return $user->isVerified();
    }

    public function update(User $user, MarketplaceListing $listing): bool
    {
        return $user->id === $listing->user_id || $user->hasPermission('manage-marketplace');
    }

    public function delete(User $user, MarketplaceListing $listing): bool
    {
        return $user->id === $listing->user_id || $user->hasPermission('manage-marketplace');
    }

    public function approve(User $user, MarketplaceListing $listing): bool
    {
        return $user->hasPermission('manage-marketplace');
    }
}
