<?php

namespace App\Notifications;

use App\Models\CateringOrder;
use Illuminate\Notifications\Notification;

class CateringOrderCancelled extends Notification
{
    public function __construct(protected CateringOrder $order, protected string $cancelledByRole) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $route = $this->order->category->name;

        $message = $this->cancelledByRole === 'admin'
            ? "Your catering order for \"{$route}\" was cancelled by our team and a refund has been issued. Reason: {$this->order->cancellation_reason}"
            : "{$this->order->customer->full_name} cancelled catering order #{$this->order->id} (\"{$route}\") and requested a refund.";

        return [
            'message' => $message,
            'icon' => 'utensils',
            'url' => $this->cancelledByRole === 'admin'
                ? route('catering.orders.show', $this->order)
                : route('admin.catering.orders.show', $this->order),
        ];
    }
}
