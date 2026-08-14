<?php

namespace App\Policies;

use App\Models\Slider;
use App\Models\User;

class SliderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage-sliders');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('manage-sliders');
    }

    public function update(User $user, Slider $slider): bool
    {
        return $user->hasPermission('manage-sliders');
    }

    public function delete(User $user, Slider $slider): bool
    {
        return $user->hasPermission('manage-sliders');
    }
}
