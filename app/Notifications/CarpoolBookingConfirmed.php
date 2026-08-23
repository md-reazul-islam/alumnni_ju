<?php

namespace App\Notifications;

use App\Models\CarpoolBooking;
use Illuminate\Notifications\Notification;

class CarpoolBookingConfirmed extends Notification
{
    public function __construct(protected CarpoolBooking $booking, protected bool $forDriver = false) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $schedule = $this->booking->schedule;
        $route = $schedule->origin . ' to ' . $schedule->destination;

        $message = $this->forDriver
            ? "{$this->booking->passenger->full_name} paid and confirmed {$this->booking->seats} seat(s) on \"{$route}\". You'll earn $" . number_format($this->booking->driver_payout_amount, 2) . '.'
            : "Your payment was received — your {$this->booking->seats} seat(s) on \"{$route}\" are confirmed.";

        return [
            'message' => $message,
            'icon' => 'car',
            'url' => $this->forDriver ? route('carpooling.driver.dashboard') : route('carpooling.bookings.index'),
        ];
    }
}
