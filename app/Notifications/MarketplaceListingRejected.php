<?php

namespace App\Notifications;

use App\Models\MarketplaceListing;
use Illuminate\Notifications\Notification;

class MarketplaceListingRejected extends Notification
{
    public function __construct(protected MarketplaceListing $listing) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your listing "' . $this->listing->title . '" was not approved.',
            'icon' => 'shopping-bag',
            'url' => route('marketplace.mine'),
        ];
    }
}
