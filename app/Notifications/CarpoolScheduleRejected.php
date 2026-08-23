<?php

namespace App\Notifications;

use App\Models\CarpoolSchedule;
use Illuminate\Notifications\Notification;

class CarpoolScheduleRejected extends Notification
{
    public function __construct(protected CarpoolSchedule $schedule) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Your trip \"{$this->schedule->origin} to {$this->schedule->destination}\" was not approved.",
            'icon' => 'car',
            'url' => route('carpooling.driver.dashboard'),
        ];
    }
}
