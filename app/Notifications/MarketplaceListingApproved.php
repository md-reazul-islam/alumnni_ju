<?php

namespace App\Notifications;

use App\Models\MarketplaceListing;
use Illuminate\Notifications\Notification;

class MarketplaceListingApproved extends Notification
{
    public function __construct(protected MarketplaceListing $listing) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your listing "' . $this->listing->title . '" has been approved and is now live.',
            'icon' => 'shopping-bag',
            'url' => route('marketplace.show', $this->listing),
        ];
    }
}
