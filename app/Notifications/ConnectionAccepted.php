<?php

namespace App\Notifications;

use App\Models\Connection;
use Illuminate\Notifications\Notification;

class ConnectionAccepted extends Notification
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
            'message' => $this->connection->recipient->full_name . ' accepted your connection request.',
            'icon' => 'user-check',
            'url' => route('connections.index'),
        ];
    }
}
