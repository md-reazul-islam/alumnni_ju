<?php

namespace App\Notifications;

use App\Models\MentorshipRequest;
use Illuminate\Notifications\Notification;

class MentorshipRequestReceived extends Notification
{
    public function __construct(protected MentorshipRequest $request)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->request->mentee->full_name . ' requested mentorship from you.',
            'icon' => 'handshake',
            'url' => route('mentorship.mine'),
        ];
    }
}
