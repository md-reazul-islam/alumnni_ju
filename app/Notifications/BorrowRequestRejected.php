<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Notifications\Notification;

class BorrowRequestRejected extends Notification
{
    public function __construct(protected BorrowRequest $borrowRequest) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your request to borrow "' . $this->borrowRequest->book->title . '" was not approved.',
            'icon' => 'book-open',
            'url' => route('library.mine'),
        ];
    }
}
