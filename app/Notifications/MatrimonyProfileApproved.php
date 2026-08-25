<?php

namespace App\Notifications;

use App\Models\MatrimonyProfile;
use Illuminate\Notifications\Notification;

class MatrimonyProfileApproved extends Notification
{
    public function __construct(protected MatrimonyProfile $profile) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "The matrimony profile for \"{$this->profile->display_name}\" has been approved and is now visible in search.",
            'icon' => 'heart',
            'url' => route('matrimony.profiles.mine'),
        ];
    }
}
