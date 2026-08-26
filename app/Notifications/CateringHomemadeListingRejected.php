<?php

namespace App\Notifications;

use App\Models\CateringHomemadeListing;
use Illuminate\Notifications\Notification;

class CateringHomemadeListingRejected extends Notification
{
    public function __construct(protected CateringHomemadeListing $listing) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your home made food listing "' . $this->listing->title . '" was not approved.',
            'icon' => 'cooking-pot',
            'url' => route('catering.homemade.mine'),
        ];
    }
}
