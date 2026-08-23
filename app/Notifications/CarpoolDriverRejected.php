<?php

namespace App\Notifications;

use App\Models\CarpoolDriverProfile;
use Illuminate\Notifications\Notification;

class CarpoolDriverRejected extends Notification
{
    public function __construct(protected CarpoolDriverProfile $profile) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your carpool driver application was not approved.',
            'icon' => 'car',
            'url' => route('carpooling.driver.become'),
        ];
    }
}
