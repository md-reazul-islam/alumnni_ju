<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Notifications\Notification;

class BorrowRequestApproved extends Notification
{
    public function __construct(protected BorrowRequest $borrowRequest) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your request to borrow "' . $this->borrowRequest->book->title . '" has been approved. Please visit the alumni office to collect it.',
            'icon' => 'book-open',
            'url' => route('library.mine'),
        ];
    }
}
