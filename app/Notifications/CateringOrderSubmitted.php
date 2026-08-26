<?php

namespace App\Notifications;

use App\Models\CateringOrder;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class CateringOrderSubmitted extends Notification
{
    public function __construct(protected CateringOrder $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->order->customer->full_name} submitted a catering order for \"{$this->order->category->name}\" ({$this->order->event_date->format('M j, Y')}). It needs pricing.",
            'icon' => 'utensils',
            'url' => Route::has('admin.catering.orders.show') ? route('admin.catering.orders.show', $this->order) : route('admin.dashboard'),
        ];
    }
}
