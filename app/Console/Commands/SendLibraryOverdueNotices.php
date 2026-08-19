<?php

namespace App\Console\Commands;

use App\Models\BorrowRequest;
use App\Notifications\BorrowDueReminder;
use Illuminate\Console\Command;

class SendLibraryOverdueNotices extends Command
{
    protected $signature = 'library:send-overdue-notices';

    protected $description = 'Notify borrowers whose library borrow period has passed its due date';

    public function handle(): int
    {
        $overdue = BorrowRequest::handedOver()
            ->whereNotNull('due_date')
            ->where('due_date', '<', today())
            ->whereNull('overdue_notified_at')
            ->with(['book', 'borrower'])
            ->get();

        foreach ($overdue as $borrowRequest) {
            $borrowRequest->borrower->notify(new BorrowDueReminder($borrowRequest, overdue: true));
            $borrowRequest->update(['overdue_notified_at' => now()]);
        }

        $this->info("Sent {$overdue->count()} overdue notice(s).");

        return self::SUCCESS;
    }
}
