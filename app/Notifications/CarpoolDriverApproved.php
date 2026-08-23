<?php

namespace App\Notifications;

use App\Models\CarpoolDriverProfile;
use Illuminate\Notifications\Notification;

class CarpoolDriverApproved extends Notification
{
    public function __construct(protected CarpoolDriverProfile $profile) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your carpool driver application has been approved. You can now post trips.',
            'icon' => 'car',
            'url' => route('carpooling.driver.dashboard'),
        ];
    }
}
