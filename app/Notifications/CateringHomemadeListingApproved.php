<?php

namespace App\Notifications;

use App\Models\CateringHomemadeListing;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class CateringHomemadeListingApproved extends Notification
{
    public function __construct(protected CateringHomemadeListing $listing) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your home made food listing "' . $this->listing->title . '" has been approved and is now live.',
            'icon' => 'cooking-pot',
            'url' => Route::has('catering.homemade.show') ? route('catering.homemade.show', $this->listing) : route('catering.homemade.mine'),
        ];
    }
}
