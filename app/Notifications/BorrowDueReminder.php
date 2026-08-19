<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Notifications\Notification;

class BorrowDueReminder extends Notification
{
    public function __construct(protected BorrowRequest $borrowRequest, protected bool $overdue = false) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->borrowRequest->book->title;
        $dueDate = $this->borrowRequest->due_date?->format('F j, Y');

        $message = $this->overdue
            ? "\"{$title}\" was due back on {$dueDate} and is now overdue. Please return it as soon as possible."
            : "Reminder: \"{$title}\" is due back on {$dueDate}.";

        return [
            'message' => $message,
            'icon' => 'book-open',
            'url' => route('library.mine'),
        ];
    }
}
