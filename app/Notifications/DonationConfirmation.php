<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Notifications\Notification;

class DonationConfirmation extends Notification
{
    public function __construct(protected Donation $donation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Thank you for your donation of $' . number_format((float) $this->donation->amount, 2) . '.',
            'icon' => 'dollar-sign',
            'url' => route('donations.index'),
        ];
    }
}
