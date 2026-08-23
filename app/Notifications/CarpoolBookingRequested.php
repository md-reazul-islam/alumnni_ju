<?php

namespace App\Notifications;

use App\Models\CarpoolBooking;
use Illuminate\Notifications\Notification;

class CarpoolBookingRequested extends Notification
{
    public function __construct(protected CarpoolBooking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $schedule = $this->booking->schedule;

        return [
            'message' => "{$this->booking->passenger->full_name} requested {$this->booking->seats} seat(s) on your trip \"{$schedule->origin} to {$schedule->destination}\".",
            'icon' => 'car',
            'url' => route('carpooling.driver.bookings.index'),
        ];
    }
}
