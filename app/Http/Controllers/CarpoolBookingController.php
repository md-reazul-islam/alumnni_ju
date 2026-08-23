<?php

namespace App\Http\Controllers;

use App\Models\CarpoolBooking;
use App\Models\CarpoolSchedule;
use App\Models\Setting;
use App\Notifications\CarpoolBookingCancelled;
use App\Notifications\CarpoolBookingRequested;
use App\Services\Carpool\CarpoolRefundService;
use App\Services\Carpool\StripeCheckoutService;
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

        if (in_array($booking->status, [CarpoolBooking::STATUS_REQUESTED, CarpoolBooking::STATUS_ACCEPTED], true)) {
            $booking->update([
                'status' => CarpoolBooking::STATUS_CANCELLED,
                'cancelled_by' => $request->user()->id,
            ]);

            return back()->with('status', 'Request withdrawn.');
        }

        abort_unless($booking->status === CarpoolBooking::STATUS_CONFIRMED, 422, 'This booking cannot be cancelled.');

        $hours = (int) Setting::get('carpooling', 'cancellation_window_hours', 24);
        $departure = $booking->schedule->departure_date->copy()->setTimeFromTimeString($booking->schedule->departure_time);

        abort_unless(
            now()->addHours($hours)->lte($departure),
            422,
            "Cancellations within {$hours} hours of departure are no longer eligible for a refund through the app. Please contact the driver directly."
        );

        // Refund must be initiated with Stripe BEFORE we release the seat or mark the booking
        // cancelled — if Stripe rejects/fails, nothing about the booking should change.
        app(CarpoolRefundService::class)->refund($booking);

        DB::transaction(function () use ($booking, $request) {
            $schedule = CarpoolSchedule::lockForUpdate()->find($booking->carpool_schedule_id);
            $schedule->decrement('seats_booked', $booking->seats);
            $schedule->driverProfile->decrement('total_earned', $booking->driver_payout_amount ?? 0);

            $booking->update([
                'status' => CarpoolBooking::STATUS_CANCELLED,
                'cancelled_by' => $request->user()->id,
                'cancellation_reason' => 'Cancelled by passenger within the refund window.',
            ]);
        });

        $booking->schedule->driverProfile->user->notify(new CarpoolBookingCancelled($booking, cancelledByRole: 'passenger'));

        return back()->with('status', 'Booking cancelled. Your refund has been initiated.');
    }

    public function pay(Request $request, CarpoolBooking $booking): RedirectResponse
    {
        abort_unless($booking->passenger_id === $request->user()->id, 403);
        abort_unless($booking->status === CarpoolBooking::STATUS_ACCEPTED, 422, 'This booking is not awaiting payment.');
        abort_if($booking->payment_deadline_at && $booking->payment_deadline_at->isPast(), 422, 'The payment window for this booking has expired.');

        $session = app(StripeCheckoutService::class)->createSession($booking);

        return redirect($session->url);
    }

    public function paymentSuccess(Request $request, CarpoolBooking $booking): View
    {
        abort_unless($booking->passenger_id === $request->user()->id, 403);

        return view('carpooling.passenger.bookings.payment-success', compact('booking'));
    }

    public function paymentCancelled(Request $request, CarpoolBooking $booking): View
    {
        abort_unless($booking->passenger_id === $request->user()->id, 403);

        return view('carpooling.passenger.bookings.payment-cancelled', compact('booking'));
    }
}
