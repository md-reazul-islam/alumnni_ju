<?php

namespace App\Notifications;

use App\Models\CateringOrder;
use Illuminate\Notifications\Notification;

class CateringOrderPriced extends Notification
{
    public function __construct(protected CateringOrder $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Your catering order for \"{$this->order->category->name}\" has been priced — total \${$this->formattedTotal()}. Review and accept or decline.",
            'icon' => 'utensils',
            'url' => route('catering.orders.show', $this->order),
        ];
    }

    protected function formattedTotal(): string
    {
        return number_format($this->order->total_amount, 2);
    }
}
