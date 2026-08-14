<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Notifications\Notification;

class AdminAnnouncementPosted extends Notification
{
    public function __construct(protected Announcement $announcement)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'New announcement: ' . $this->announcement->title,
            'icon' => 'megaphone',
            'url' => route('dashboard'),
        ];
    }
}
