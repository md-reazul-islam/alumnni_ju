<?php

namespace App\Notifications;

use App\Models\CateringOrder;
use Illuminate\Notifications\Notification;

class CateringOrderDeclined extends Notification
{
    public function __construct(protected CateringOrder $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->order->customer->full_name} declined the invoice for catering order #{$this->order->id} (\"{$this->order->category->name}\").",
            'icon' => 'utensils',
            'url' => route('admin.catering.orders.show', $this->order),
        ];
    }
}
