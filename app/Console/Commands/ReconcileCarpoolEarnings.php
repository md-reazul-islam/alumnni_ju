<?php

namespace App\Console\Commands;

use App\Models\CarpoolBooking;
use App\Models\CarpoolDriverProfile;
use App\Services\AuditLogger;
use Illuminate\Console\Command;

class ReconcileCarpoolEarnings extends Command
{
    protected $signature = 'carpool:reconcile-earnings';

    protected $description = "Recompute each driver's total_earned from actual paid bookings and correct any drift";

    public function handle(): int
    {
        $corrected = 0;

        CarpoolDriverProfile::chunk(100, function ($profiles) use (&$corrected) {
            foreach ($profiles as $profile) {
                $actual = (float) CarpoolBooking::where('payment_status', CarpoolBooking::PAYMENT_PAID)
                    ->whereHas('schedule', fn ($q) => $q->where('carpool_driver_profile_id', $profile->id))
                    ->sum('driver_payout_amount');

                if (abs($actual - (float) $profile->total_earned) > 0.01) {
                    $previous = $profile->total_earned;
                    $profile->update(['total_earned' => $actual]);

                    AuditLogger::log(
                        'corrected_carpool_driver_earnings',
                        $profile,
                        "Corrected total_earned for driver profile #{$profile->id} from \${$previous} to \${$actual} (drift detected during reconciliation)."
                    );

                    $corrected++;
                }
            }
        });

        $this->info("Reconciled carpool driver earnings. Corrected {$corrected} driver(s).");

        return self::SUCCESS;
    }
}
