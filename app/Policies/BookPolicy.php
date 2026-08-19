<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function create(User $user): bool
    {
        return $user->isVerified();
    }

    public function update(User $user, Book $book): bool
    {
        return $user->id === $book->donor_id || $user->hasPermission('manage-library');
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->id === $book->donor_id || $user->hasPermission('manage-library');
    }

    public function review(User $user, Book $book): bool
    {
        return $user->hasPermission('manage-library');
    }
}
