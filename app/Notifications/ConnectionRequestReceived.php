<?php

namespace App\Notifications;

use App\Models\Connection;
use Illuminate\Notifications\Notification;

class ConnectionRequestReceived extends Notification
{
    public function __construct(protected Connection $connection)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->connection->requester->full_name . ' sent you a connection request.',
            'icon' => 'user-plus',
            'url' => route('connections.index'),
        ];
    }
}
