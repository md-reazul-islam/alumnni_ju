<?php

namespace App\Http\Controllers;

use App\Models\CarpoolBooking;
use App\Models\CarpoolSchedule;
use App\Models\Setting;
use App\Notifications\CarpoolBookingAccepted;
use App\Notifications\CarpoolBookingDeclined;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DriverBookingController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $request->user()->carpoolDriverProfile;
        abort_unless($profile, 404);

        $bookings = CarpoolBooking::whereIn('carpool_schedule_id', $profile->schedules()->pluck('id'))
            ->with(['schedule', 'passenger'])
            ->latest()
            ->paginate(20);

        return view('carpooling.driver.bookings.index', compact('bookings'));
    }

    public function accept(Request $request, CarpoolBooking $booking): RedirectResponse
    {
        $this->ensureOwnsBooking($request, $booking);
        abort_unless($booking->status === CarpoolBooking::STATUS_REQUESTED, 422, 'This request has already been responded to.');

        DB::transaction(function () use ($booking) {
            $schedule = CarpoolSchedule::lockForUpdate()->find($booking->carpool_schedule_id);

            abort_if($schedule->seatsRemaining() < $booking->seats, 422, 'Not enough seats remaining to accept this request.');

            $minutes = (int) Setting::get('carpooling', 'payment_window_minutes', 30);

            $booking->update([
                'status' => CarpoolBooking::STATUS_ACCEPTED,
                'driver_responded_at' => now(),
                'payment_deadline_at' => now()->addMinutes($minutes),
            ]);
        });

        $booking->passenger->notify(new CarpoolBookingAccepted($booking));

        return back()->with('status', 'Request accepted. The passenger has been notified to pay.');
    }

    public function decline(Request $request, CarpoolBooking $booking): RedirectResponse
    {
        $this->ensureOwnsBooking($request, $booking);
        abort_unless($booking->status === CarpoolBooking::STATUS_REQUESTED, 422, 'This request has already been responded to.');

        $booking->update([
            'status' => CarpoolBooking::STATUS_DECLINED,
            'driver_responded_at' => now(),
        ]);

        $booking->passenger->notify(new CarpoolBookingDeclined($booking));

        return back()->with('status', 'Request declined.');
    }

    protected function ensureOwnsBooking(Request $request, CarpoolBooking $booking): void
    {
        abort_unless($booking->schedule->driverProfile->user_id === $request->user()->id, 403);
    }
}
