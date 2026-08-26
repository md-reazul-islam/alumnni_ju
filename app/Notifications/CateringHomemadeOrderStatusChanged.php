<?php

namespace App\Notifications;

use App\Models\CateringHomemadeOrder;
use Illuminate\Notifications\Notification;

class CateringHomemadeOrderStatusChanged extends Notification
{
    public function __construct(protected CateringHomemadeOrder $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your order for "' . $this->order->listing->title . '" is now ' . $this->order->status . '.',
            'icon' => 'cooking-pot',
            'url' => route('catering.homemade.show', $this->order->listing),
        ];
    }
}
