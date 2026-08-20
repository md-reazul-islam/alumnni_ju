<?php

namespace App\Notifications;

use App\Models\MarketplaceOrder;
use Illuminate\Notifications\Notification;

class MarketplaceOrderStatusChanged extends Notification
{
    public function __construct(protected MarketplaceOrder $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your inquiry about "' . $this->order->listing->title . '" is now ' . $this->order->status . '.',
            'icon' => 'shopping-bag',
            'url' => route('marketplace.show', $this->order->listing),
        ];
    }
}
