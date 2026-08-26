<?php

namespace App\Notifications;

use App\Models\CateringOrder;
use Illuminate\Notifications\Notification;

class CateringOrderRejected extends Notification
{
    public function __construct(protected CateringOrder $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Your catering order for \"{$this->order->category->name}\" could not be fulfilled: {$this->order->cancellation_reason}",
            'icon' => 'utensils',
            'url' => route('catering.orders.show', $this->order),
        ];
    }
}
