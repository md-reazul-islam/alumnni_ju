<?php

namespace App\Policies;

use App\Models\AlumniProfile;
use App\Models\Connection;
use App\Models\User;

class AlumniProfilePolicy
{
    public function view(?User $user, AlumniProfile $profile): bool
    {
        if ($profile->profile_visibility === AlumniProfile::VISIBILITY_PUBLIC) {
            return true;
        }

        if (! $user) {
            return false;
        }

        if ($user->id === $profile->user_id || $user->hasPermission('manage-alumni') || $user->hasPermission('moderate-alumni-profiles')) {
            return true;
        }

        if ($profile->profile_visibility === AlumniProfile::VISIBILITY_ALUMNI) {
            return $user->isVerified();
        }

        // Private: visible only to accepted connections.
        return Connection::where('status', Connection::STATUS_ACCEPTED)
            ->where(function ($query) use ($user, $profile) {
                $query->where('requester_id', $user->id)->where('recipient_id', $profile->user_id);
            })
            ->orWhere(function ($query) use ($user, $profile) {
                $query->where('requester_id', $profile->user_id)->where('recipient_id', $user->id);
            })
            ->exists();
    }

    public function update(User $user, AlumniProfile $profile): bool
    {
        return $user->id === $profile->user_id || $user->hasPermission('manage-alumni');
    }

    public function verify(User $user, AlumniProfile $profile): bool
    {
        return $user->hasPermission('manage-alumni');
    }

    public function delete(User $user, AlumniProfile $profile): bool
    {
        return $user->hasPermission('manage-alumni');
    }
}
