<?php

namespace App\Notifications;

use App\Models\JobPosting;
use Illuminate\Notifications\Notification;

class JobApproved extends Notification
{
    public function __construct(protected JobPosting $job)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your job posting "' . $this->job->title . '" has been approved and is now live.',
            'icon' => 'briefcase',
            'url' => route('jobs.show', $this->job),
        ];
    }
}
