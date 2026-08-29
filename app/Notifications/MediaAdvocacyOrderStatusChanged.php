<?php

namespace App\Notifications;

use App\Models\MediaAdvocacyOrder;
use Illuminate\Notifications\Notification;

class MediaAdvocacyOrderStatusChanged extends Notification
{
    public function __construct(protected MediaAdvocacyOrder $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your request for "' . $this->order->category->name . '" is now ' . $this->order->status . '.',
            'icon' => 'megaphone',
            'url' => route('media-advocacy.index'),
        ];
    }
}
