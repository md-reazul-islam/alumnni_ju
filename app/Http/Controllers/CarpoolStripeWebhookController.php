<?php

namespace App\Http\Controllers;

use App\Models\CarpoolBooking;
use App\Models\CarpoolPayment;
use App\Models\CarpoolSchedule;
use App\Models\Setting;
use App\Notifications\CarpoolBookingConfirmed;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class CarpoolStripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                (string) config('services.stripe.webhook_secret')
            );
        } catch (SignatureVerificationException|UnexpectedValueException $e) {
            Log::warning('Carpool Stripe webhook rejected: ' . $e->getMessage());

            return response('Invalid signature', 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            'checkout.session.expired' => $this->handleCheckoutExpired($event->data->object),
            'charge.refunded' => $this->handleChargeRefunded($event->data->object),
            default => null,
        };

        return response('OK', 200);
    }

    protected function handleCheckoutCompleted(object $session): void
    {
        $confirmed = DB::transaction(function () use ($session) {
            $payment = CarpoolPayment::where('stripe_checkout_session_id', $session->id)->lockForUpdate()->first();

            if (! $payment || $payment->status === CarpoolPayment::STATUS_SUCCEEDED) {
                return null;
            }

            $booking = CarpoolBooking::find($payment->carpool_booking_id);

            if (! $booking || $booking->payment_status === CarpoolBooking::PAYMENT_PAID) {
                return null;
            }

            $schedule = CarpoolSchedule::lockForUpdate()->find($booking->carpool_schedule_id);

            $otherCommittedSeats = $schedule->seats_booked + $schedule->heldSeats($booking->id);

            if ($otherCommittedSeats + $booking->seats > $schedule->seats_offered) {
                // Should not happen — capacity was already reserved at accept time — but never silently
                // overbook a car. Leave the payment recorded as succeeded so admin can see money moved
                // and manually resolve; the booking itself is not force-confirmed.
                Log::error("Carpool booking {$booking->id} paid but schedule {$schedule->id} has no room left.");
                $payment->update(['status' => CarpoolPayment::STATUS_SUCCEEDED, 'gateway_reference' => $session->payment_intent ?? null, 'paid_at' => now()]);

                return null;
            }

            $commissionPercentage = (float) Setting::get('carpooling', 'commission_percentage', 10);
            $commissionAmount = round((float) $booking->total_fare * $commissionPercentage / 100, 2);
            $payoutAmount = round((float) $booking->total_fare - $commissionAmount, 2);

            $payment->update([
                'status' => CarpoolPayment::STATUS_SUCCEEDED,
                'gateway_reference' => $session->payment_intent ?? null,
                'paid_at' => now(),
            ]);

            $booking->update([
                'status' => CarpoolBooking::STATUS_CONFIRMED,
                'payment_status' => CarpoolBooking::PAYMENT_PAID,
                'commission_percentage_snapshot' => $commissionPercentage,
                'commission_amount' => $commissionAmount,
                'driver_payout_amount' => $payoutAmount,
            ]);

            $schedule->increment('seats_booked', $booking->seats);
            $schedule->driverProfile->increment('total_earned', $payoutAmount);

            return $booking;
        });

        if ($confirmed) {
            $confirmed->passenger->notify(new CarpoolBookingConfirmed($confirmed));
            $confirmed->schedule->driverProfile->user->notify(new CarpoolBookingConfirmed($confirmed, forDriver: true));
        }
    }

    protected function handleCheckoutExpired(object $session): void
    {
        CarpoolPayment::where('stripe_checkout_session_id', $session->id)
            ->where('status', CarpoolPayment::STATUS_PENDING)
            ->update([
                'status' => CarpoolPayment::STATUS_FAILED,
                'failure_reason' => 'Checkout session expired before payment was completed.',
            ]);
    }

    protected function handleChargeRefunded(object $charge): void
    {
        DB::transaction(function () use ($charge) {
            $payment = CarpoolPayment::where('gateway_reference', $charge->payment_intent)->lockForUpdate()->first();

            if (! $payment || $payment->status === CarpoolPayment::STATUS_REFUNDED) {
                return;
            }

            $booking = CarpoolBooking::find($payment->carpool_booking_id);

            if (! $booking) {
                return;
            }

            $payment->update(['status' => CarpoolPayment::STATUS_REFUNDED, 'refunded_at' => now()]);

            if ($booking->payment_status === CarpoolBooking::PAYMENT_PAID) {
                $schedule = CarpoolSchedule::lockForUpdate()->find($booking->carpool_schedule_id);
                $schedule->decrement('seats_booked', $booking->seats);
                $schedule->driverProfile->decrement('total_earned', $booking->driver_payout_amount ?? 0);
            }

            $booking->update([
                'status' => CarpoolBooking::STATUS_CANCELLED,
                'payment_status' => CarpoolBooking::PAYMENT_REFUNDED,
            ]);
        });
    }
}
