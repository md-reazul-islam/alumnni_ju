<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarpoolBooking;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarpoolPayoutController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-carpooling'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $bookings = CarpoolBooking::payoutPending()
            ->with(['schedule.driverProfile.user', 'schedule.car', 'passenger'])
            ->oldest('driver_responded_at')
            ->paginate(20);

        $totalsByDriver = CarpoolBooking::payoutPending()
            ->join('carpool_schedules', 'carpool_schedules.id', '=', 'carpool_bookings.carpool_schedule_id')
            ->join('carpool_driver_profiles', 'carpool_driver_profiles.id', '=', 'carpool_schedules.carpool_driver_profile_id')
            ->join('users', 'users.id', '=', 'carpool_driver_profiles.user_id')
            ->selectRaw('users.id as user_id, users.first_name, users.last_name, SUM(carpool_bookings.driver_payout_amount) as total_due, COUNT(*) as trip_count')
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->orderByDesc('total_due')
            ->get();

        return view('admin.carpooling.payouts.index', compact('bookings', 'totalsByDriver'));
    }

    public function markPaid(Request $request, CarpoolBooking $carpoolBooking): RedirectResponse
    {
        $this->ensurePermission($request);

        abort_unless($carpoolBooking->payment_status === CarpoolBooking::PAYMENT_PAID, 422, "This booking hasn't been paid by the passenger yet.");
        abort_if($carpoolBooking->payout_status === CarpoolBooking::PAYOUT_PAID, 422, 'This payout has already been marked paid.');

        $carpoolBooking->update([
            'payout_status' => CarpoolBooking::PAYOUT_PAID,
            'paid_out_at' => now(),
            'paid_out_by' => $request->user()->id,
        ]);

        AuditLogger::log(
            'marked_carpool_payout_paid',
            $carpoolBooking,
            "Marked a \${$carpoolBooking->driver_payout_amount} payout paid to {$carpoolBooking->schedule->driverProfile->user->full_name} for booking #{$carpoolBooking->id}."
        );

        return back()->with('status', 'Payout marked as paid.');
    }
}
