<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Notifications\Notification;

class NewMessageReceived extends Notification
{
    public function __construct(protected Message $message)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message->sender->full_name . ' sent you a message.',
            'icon' => 'message-circle',
            'url' => route('messages.index', $this->message->conversation_id),
        ];
    }
}
