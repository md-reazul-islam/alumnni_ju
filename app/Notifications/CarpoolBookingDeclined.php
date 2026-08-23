<?php

namespace App\Notifications;

use App\Models\CarpoolBooking;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class CarpoolBookingDeclined extends Notification
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
            'message' => "Your request for \"{$schedule->origin} to {$schedule->destination}\" was declined by the driver.",
            'icon' => 'car',
            'url' => Route::has('carpooling.search') ? route('carpooling.search') : route('dashboard'),
        ];
    }
}
