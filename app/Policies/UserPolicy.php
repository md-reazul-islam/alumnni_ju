<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage-alumni') || $user->hasPermission('manage-users');
    }

    public function view(User $user, User $target): bool
    {
        return $user->id === $target->id || $user->hasPermission('manage-alumni');
    }

    public function update(User $user, User $target): bool
    {
        return $user->id === $target->id || $user->hasPermission('manage-alumni');
    }

    public function manageAccountStatus(User $user, User $target): bool
    {
        // Admins may only act on plain alumni accounts, never on fellow staff/admins.
        return $user->hasPermission('manage-alumni') && $target->isAlumni();
    }

    public function delete(User $user, User $target): bool
    {
        return $user->hasPermission('manage-administrators') && $user->id !== $target->id;
    }
}
