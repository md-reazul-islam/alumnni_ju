<?php

namespace App\Http\Controllers;

use App\Models\CateringOrder;
use App\Models\CateringPayment;
use App\Models\User;
use App\Notifications\CateringOrderPaid;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class CateringStripeWebhookController extends Controller
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
            Log::warning('Catering Stripe webhook rejected: ' . $e->getMessage());

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
            $payment = CateringPayment::where('stripe_checkout_session_id', $session->id)->lockForUpdate()->first();

            if (! $payment || $payment->status === CateringPayment::STATUS_SUCCEEDED) {
                return null;
            }

            $order = CateringOrder::lockForUpdate()->find($payment->catering_order_id);

            if (! $order || $order->payment_status === CateringOrder::PAYMENT_PAID) {
                return null;
            }

            $payment->update([
                'status' => CateringPayment::STATUS_SUCCEEDED,
                'gateway_reference' => $session->payment_intent ?? null,
                'paid_at' => now(),
            ]);

            $order->update([
                'status' => CateringOrder::STATUS_ACCEPTED,
                'payment_status' => CateringOrder::PAYMENT_PAID,
                'customer_responded_at' => now(),
            ]);

            return $order;
        });

        if ($confirmed) {
            $confirmed->customer->notify(new CateringOrderPaid($confirmed));

            $admins = User::withPermission('manage-catering')->get();
            Notification::send($admins, new CateringOrderPaid($confirmed, forAdmin: true));
        }
    }

    protected function handleCheckoutExpired(object $session): void
    {
        CateringPayment::where('stripe_checkout_session_id', $session->id)
            ->where('status', CateringPayment::STATUS_PENDING)
            ->update([
                'status' => CateringPayment::STATUS_FAILED,
                'failure_reason' => 'Checkout session expired before payment was completed.',
            ]);
    }

    protected function handleChargeRefunded(object $charge): void
    {
        DB::transaction(function () use ($charge) {
            $payment = CateringPayment::where('gateway_reference', $charge->payment_intent)->lockForUpdate()->first();

            if (! $payment || $payment->status === CateringPayment::STATUS_REFUNDED) {
                return;
            }

            $order = CateringOrder::lockForUpdate()->find($payment->catering_order_id);

            if (! $order) {
                return;
            }

            $payment->update(['status' => CateringPayment::STATUS_REFUNDED, 'refunded_at' => now()]);

            if ($order->status !== CateringOrder::STATUS_CANCELLED) {
                // Refund wasn't initiated through our own cancellation flow (e.g. issued directly
                // from the Stripe dashboard) — reflect it here since nothing else will.
                $order->update([
                    'status' => CateringOrder::STATUS_CANCELLED,
                    'cancellation_reason' => 'Refunded via Stripe.',
                    'cancelled_at' => now(),
                ]);
            }

            $order->update(['payment_status' => CateringOrder::PAYMENT_REFUNDED]);
        });
    }
}
