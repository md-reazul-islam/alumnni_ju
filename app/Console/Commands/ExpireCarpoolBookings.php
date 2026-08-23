<?php

namespace App\Console\Commands;

use App\Models\CarpoolBooking;
use App\Notifications\CarpoolBookingExpired;
use Illuminate\Console\Command;

class ExpireCarpoolBookings extends Command
{
    protected $signature = 'carpool:expire-bookings';

    protected $description = 'Expire accepted carpool bookings whose payment window has passed without payment';

    public function handle(): int
    {
        $expired = CarpoolBooking::where('status', CarpoolBooking::STATUS_ACCEPTED)
            ->where('payment_status', CarpoolBooking::PAYMENT_UNPAID)
            ->whereNotNull('payment_deadline_at')
            ->where('payment_deadline_at', '<', now())
            ->with('passenger', 'schedule')
            ->get();

        foreach ($expired as $booking) {
            $booking->update(['status' => CarpoolBooking::STATUS_EXPIRED]);
            $booking->passenger->notify(new CarpoolBookingExpired($booking));
        }

        $this->info("Expired {$expired->count()} unpaid carpool booking(s).");

        return self::SUCCESS;
    }
}
