<?php

namespace App\Notifications;

use App\Models\MatrimonyInterest;
use Illuminate\Notifications\Notification;

class MatrimonyInterestExpired extends Notification
{
    public function __construct(protected MatrimonyInterest $interest) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Your interest request for \"{$this->interest->profile->display_name}\" expired without a response.",
            'icon' => 'heart',
            'url' => route('matrimony.search'),
        ];
    }
}
