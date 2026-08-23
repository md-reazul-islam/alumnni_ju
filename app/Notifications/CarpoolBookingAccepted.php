<?php

namespace App\Notifications;

use App\Models\CarpoolBooking;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class CarpoolBookingAccepted extends Notification
{
    public function __construct(protected CarpoolBooking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $schedule = $this->booking->schedule;
        $deadline = $this->booking->payment_deadline_at?->format('M j, g:i A');

        return [
            'message' => "Your request for {$this->booking->seats} seat(s) on \"{$schedule->origin} to {$schedule->destination}\" was accepted."
                . ($deadline ? " Pay by {$deadline} to confirm your seat." : ''),
            'icon' => 'car',
            'url' => Route::has('carpooling.bookings.index') ? route('carpooling.bookings.index') : route('dashboard'),
        ];
    }
}
