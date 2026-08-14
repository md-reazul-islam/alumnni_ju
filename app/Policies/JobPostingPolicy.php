<?php

namespace App\Policies;

use App\Models\JobPosting;
use App\Models\User;

class JobPostingPolicy
{
    public function create(User $user): bool
    {
        return $user->isVerified();
    }

    public function update(User $user, JobPosting $job): bool
    {
        return $user->id === $job->posted_by || $user->hasPermission('manage-jobs');
    }

    public function delete(User $user, JobPosting $job): bool
    {
        return $user->id === $job->posted_by || $user->hasPermission('manage-jobs');
    }

    public function approve(User $user, JobPosting $job): bool
    {
        return $user->hasPermission('manage-jobs');
    }
}
