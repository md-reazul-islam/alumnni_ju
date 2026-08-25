<?php

namespace App\Console\Commands;

use App\Models\MatrimonyInterest;
use App\Models\Setting;
use App\Notifications\MatrimonyInterestExpired;
use Illuminate\Console\Command;

class ExpireMatrimonyInterests extends Command
{
    protected $signature = 'matrimony:expire-interests';

    protected $description = 'Expire pending matrimony interest requests older than the configured window';

    public function handle(): int
    {
        $days = (int) Setting::get('matrimony', 'interest_expiry_days', 30);

        $expired = MatrimonyInterest::where('status', MatrimonyInterest::STATUS_PENDING)
            ->where('created_at', '<', now()->subDays($days))
            ->with(['requester', 'profile'])
            ->get();

        foreach ($expired as $interest) {
            $interest->update(['status' => MatrimonyInterest::STATUS_EXPIRED]);
            $interest->requester->notify(new MatrimonyInterestExpired($interest));
        }

        $this->info("Expired {$expired->count()} pending matrimony interest request(s).");

        return self::SUCCESS;
    }
}
