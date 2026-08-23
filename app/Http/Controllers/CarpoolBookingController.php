<?php

namespace App\Http\Controllers;

use App\Models\CarpoolBooking;
use App\Models\CarpoolSchedule;
use App\Notifications\CarpoolBookingRequested;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CarpoolBookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = $request->user()->carpoolBookings()
            ->with(['schedule.car', 'schedule.driverProfile.user'])
            ->latest()
            ->paginate(15);

        return view('carpooling.passenger.bookings.index', compact('bookings'));
    }

    public function store(Request $request, CarpoolSchedule $schedule): RedirectResponse
    {
        $data = $request->validate([
            'seats' => ['nullable', 'integer', 'min:1', 'max:4'],
        ]);
        $seats = $data['seats'] ?? 1;

        abort_if($schedule->driverProfile->user_id === $request->user()->id, 422, 'You cannot book a seat on your own trip.');
        abort_unless($schedule->status === CarpoolSchedule::STATUS_APPROVED, 422, 'This trip is not open for booking.');

        $existing = CarpoolBooking::where('carpool_schedule_id', $schedule->id)
            ->where('passenger_id', $request->user()->id)
            ->first();

        if ($existing && in_array($existing->status, [
            CarpoolBooking::STATUS_REQUESTED,
            CarpoolBooking::STATUS_ACCEPTED,
            CarpoolBooking::STATUS_CONFIRMED,
            CarpoolBooking::STATUS_COMPLETED,
        ], true)) {
            return back()->with('status', 'You already have a request on this trip.');
        }

        $booking = DB::transaction(function () use ($schedule, $seats, $request, $existing) {
            $lockedSchedule = CarpoolSchedule::lockForUpdate()->find($schedule->id);

            abort_if($lockedSchedule->seatsRemaining() < $seats, 422, 'Not enough seats remaining on this trip.');

            $attributes = [
                'seats' => $seats,
                'seat_price_snapshot' => $lockedSchedule->price_per_seat,
                'total_fare' => $lockedSchedule->price_per_seat * $seats,
                'status' => CarpoolBooking::STATUS_REQUESTED,
                'driver_responded_at' => null,
                'payment_deadline_at' => null,
                'payment_status' => CarpoolBooking::PAYMENT_UNPAID,
            ];

            if ($existing) {
                $existing->update($attributes);

                return $existing;
            }

            return CarpoolBooking::create($attributes + [
                'carpool_schedule_id' => $lockedSchedule->id,
                'passenger_id' => $request->user()->id,
            ]);
        });

        $schedule->driverProfile->user->notify(new CarpoolBookingRequested($booking));

        return back()->with('status', 'Seat request sent to the driver.');
    }

    public function cancel(Request $request, CarpoolBooking $booking): RedirectResponse
    {
        abort_unless($booking->passenger_id === $request->user()->id, 403);
        abort_unless($booking->status === CarpoolBooking::STATUS_REQUESTED, 422, 'This request can no longer be withdrawn — it has already been responded to.');

        $booking->update(['status' => CarpoolBooking::STATUS_CANCELLED]);

        return back()->with('status', 'Request withdrawn.');
    }
}
