<?php

namespace App\Notifications;

use App\Models\Book;
use Illuminate\Notifications\Notification;

class BookDonationApproved extends Notification
{
    public function __construct(protected Book $book) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your donated book "' . $this->book->title . '" has been approved and is now listed in the library.',
            'icon' => 'book-open',
            'url' => route('library.show', $this->book),
        ];
    }
}
