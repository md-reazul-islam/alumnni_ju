<?php

namespace App\Notifications;

use App\Models\CateringOrder;
use Illuminate\Notifications\Notification;

class CateringOrderPaid extends Notification
{
    public function __construct(protected CateringOrder $order, protected bool $forAdmin = false) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $route = $this->order->category->name;
        $total = number_format($this->order->total_amount, 2);

        $message = $this->forAdmin
            ? "{$this->order->customer->full_name} paid \${$total} for catering order #{$this->order->id} (\"{$route}\"). It's confirmed."
            : "Your payment of \${$total} for \"{$route}\" was received — your catering order is confirmed.";

        return [
            'message' => $message,
            'icon' => 'utensils',
            'url' => $this->forAdmin ? route('admin.catering.orders.show', $this->order) : route('catering.orders.show', $this->order),
        ];
    }
}
