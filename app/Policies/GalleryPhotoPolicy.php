<?php

namespace App\Policies;

use App\Models\GalleryPhoto;
use App\Models\User;

class GalleryPhotoPolicy
{
    public function create(User $user): bool
    {
        return $user->isVerified();
    }

    public function update(User $user, GalleryPhoto $photo): bool
    {
        return $user->id === $photo->user_id || $user->hasPermission('manage-gallery');
    }

    public function delete(User $user, GalleryPhoto $photo): bool
    {
        return $user->id === $photo->user_id || $user->hasPermission('manage-gallery');
    }

    public function review(User $user, GalleryPhoto $photo): bool
    {
        return $user->hasPermission('manage-gallery');
    }
}
