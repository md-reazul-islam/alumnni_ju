<?php

namespace App\Services\Carpool;

use App\Models\CarpoolBooking;
use App\Models\CarpoolPayment;
use RuntimeException;
use Stripe\StripeClient;

class CarpoolRefundService
{
    protected StripeClient $client;

    public function __construct()
    {
        $secret = config('services.stripe.secret');

        if (! $secret) {
            throw new RuntimeException('Stripe secret key is not configured. Set STRIPE_SECRET in .env.');
        }

        $this->client = new StripeClient($secret);
    }

    /**
     * Initiates the refund with Stripe. The actual booking/payment status update and seat release
     * happen via the charge.refunded webhook (the single source of truth for money having moved) —
     * unless the caller has already released the seat synchronously, in which case the webhook
     * just reconciles the payment record.
     */
    public function refund(CarpoolBooking $booking): void
    {
        $payment = $booking->payments()->where('status', CarpoolPayment::STATUS_SUCCEEDED)->latest()->first();

        if (! $payment || ! $payment->gateway_reference) {
            throw new RuntimeException('No successful payment found to refund for this booking.');
        }

        $this->client->refunds->create([
            'payment_intent' => $payment->gateway_reference,
        ]);
    }
}
