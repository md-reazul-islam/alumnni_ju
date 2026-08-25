<?php

namespace App\Notifications;

use App\Models\MatrimonyInterest;
use Illuminate\Notifications\Notification;

class MatrimonyInterestReceived extends Notification
{
    public function __construct(protected MatrimonyInterest $interest) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->interest->requester->full_name} sent an interest request for your matrimony profile \"{$this->interest->profile->display_name}\".",
            'icon' => 'heart',
            'url' => route('matrimony.interests.mine'),
        ];
    }
}
