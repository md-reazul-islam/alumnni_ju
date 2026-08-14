<?php

namespace App\Policies;

use App\Models\AlumniStory;
use App\Models\User;

class AlumniStoryPolicy
{
    public function create(User $user): bool
    {
        return $user->isVerified() && $user->alumniProfile;
    }

    public function update(User $user, AlumniStory $story): bool
    {
        return $user->id === $story->alumniProfile->user_id || $user->hasPermission('manage-stories');
    }

    public function delete(User $user, AlumniStory $story): bool
    {
        return $user->id === $story->alumniProfile->user_id || $user->hasPermission('manage-stories');
    }

    public function review(User $user, AlumniStory $story): bool
    {
        return $user->hasPermission('manage-stories');
    }
}
