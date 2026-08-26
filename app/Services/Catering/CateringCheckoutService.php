<?php

namespace App\Services\Catering;

use App\Models\CateringOrder;
use App\Models\CateringPayment;
use RuntimeException;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class CateringCheckoutService
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

    public function createSession(CateringOrder $order): Session
    {
        $session = $this->client->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'client_reference_id' => (string) $order->id,
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => (int) round($order->total_amount * 100),
                    'product_data' => [
                        'name' => "Catering order: {$order->category->name}",
                        'description' => 'Event date ' . $order->event_date->format('M j, Y') . ($order->guest_count ? " · {$order->guest_count} guests" : ''),
                    ],
                ],
            ]],
            'success_url' => route('catering.orders.payment-success', $order) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('catering.orders.payment-cancelled', $order),
            'metadata' => [
                'catering_order_id' => (string) $order->id,
            ],
        ]);

        CateringPayment::create([
            'catering_order_id' => $order->id,
            'amount' => $order->total_amount,
            'currency' => 'usd',
            'status' => CateringPayment::STATUS_PENDING,
            'gateway' => 'stripe',
            'stripe_checkout_session_id' => $session->id,
        ]);

        return $session;
    }
}
