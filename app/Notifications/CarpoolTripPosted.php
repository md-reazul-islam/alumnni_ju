<?php

namespace App\Notifications;

use App\Models\CarpoolSchedule;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class CarpoolTripPosted extends Notification
{
    public function __construct(protected CarpoolSchedule $schedule) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "New carpool trip: {$this->schedule->origin} to {$this->schedule->destination} on {$this->schedule->departure_date->format('M j, Y')} at "
                . \Illuminate\Support\Carbon::parse($this->schedule->departure_time)->format('g:i A') . '.',
            'icon' => 'car',
            'url' => Route::has('carpooling.search')
                ? route('carpooling.search', ['date' => $this->schedule->departure_date->format('Y-m-d')])
                : route('dashboard'),
        ];
    }
}
