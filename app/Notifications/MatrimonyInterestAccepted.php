<?php

namespace App\Notifications;

use App\Models\MatrimonyInterest;
use Illuminate\Notifications\Notification;

class MatrimonyInterestAccepted extends Notification
{
    public function __construct(protected MatrimonyInterest $interest) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "\"{$this->interest->profile->display_name}\" accepted your interest request. You can now see their full profile and message them.",
            'icon' => 'heart',
            'url' => route('matrimony.interests.mine'),
        ];
    }
}
