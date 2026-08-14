<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ProfileVerified extends Notification
{
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your alumni account has been verified. Welcome to the network!',
            'icon' => 'badge-check',
            'url' => route('dashboard'),
        ];
    }
}
