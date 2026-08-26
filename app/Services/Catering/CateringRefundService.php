<?php

namespace App\Services\Catering;

use App\Models\CateringOrder;
use App\Models\CateringPayment;
use RuntimeException;
use Stripe\StripeClient;

class CateringRefundService
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
     * Initiates the refund with Stripe. The actual order/payment status update happens via the
     * charge.refunded webhook (the single source of truth for money having moved) — unless the
     * caller has already updated it synchronously, in which case the webhook just reconciles.
     */
    public function refund(CateringOrder $order): void
    {
        $payment = $order->payments()->where('status', CateringPayment::STATUS_SUCCEEDED)->latest()->first();

        if (! $payment || ! $payment->gateway_reference) {
            throw new RuntimeException('No successful payment found to refund for this order.');
        }

        $this->client->refunds->create([
            'payment_intent' => $payment->gateway_reference,
        ]);
    }
}
