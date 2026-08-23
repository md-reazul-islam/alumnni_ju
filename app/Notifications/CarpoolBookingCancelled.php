<?php

namespace App\Notifications;

use App\Models\CarpoolBooking;
use Illuminate\Notifications\Notification;

class CarpoolBookingCancelled extends Notification
{
    public function __construct(protected CarpoolBooking $booking, protected string $cancelledByRole) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $schedule = $this->booking->schedule;
        $route = $schedule->origin . ' to ' . $schedule->destination;

        $message = $this->cancelledByRole === 'driver'
            ? "The driver cancelled your confirmed seat on \"{$route}\". A refund has been issued."
            : "{$this->booking->passenger->full_name} cancelled their confirmed seat on \"{$route}\".";

        return [
            'message' => $message,
            'icon' => 'car',
            'url' => $this->cancelledByRole === 'driver'
                ? route('carpooling.bookings.index')
                : route('carpooling.driver.dashboard'),
        ];
    }
}
