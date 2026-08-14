<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Notifications\Notification;

class EventRegistrationConfirmed extends Notification
{
    public function __construct(protected Event $event)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'You are registered for ' . $this->event->title . ' on ' . $this->event->event_date->format('M d, Y') . '.',
            'icon' => 'calendar-check',
            'url' => route('events.show', $this->event),
        ];
    }
}
