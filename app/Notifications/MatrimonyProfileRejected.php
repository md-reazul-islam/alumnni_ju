<?php

namespace App\Notifications;

use App\Models\MatrimonyProfile;
use Illuminate\Notifications\Notification;

class MatrimonyProfileRejected extends Notification
{
    public function __construct(protected MatrimonyProfile $profile) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "The matrimony profile for \"{$this->profile->display_name}\" was not approved.",
            'icon' => 'heart',
            'url' => route('matrimony.profiles.mine'),
        ];
    }
}
