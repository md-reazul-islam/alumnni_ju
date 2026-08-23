<?php

namespace App\Services\Carpool;

use App\Models\CarpoolBooking;
use App\Models\CarpoolPayment;
use Illuminate\Support\Carbon;
use RuntimeException;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeCheckoutService
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

    public function createSession(CarpoolBooking $booking): Session
    {
        $schedule = $booking->schedule;

        $session = $this->client->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'client_reference_id' => (string) $booking->id,
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => (int) round($booking->total_fare * 100),
                    'product_data' => [
                        'name' => "Carpool seat: {$schedule->origin} to {$schedule->destination}",
                        'description' => $schedule->departure_date->format('M j, Y') . ' at ' . Carbon::parse($schedule->departure_time)->format('g:i A')
                            . " ({$booking->seats} seat(s))",
                    ],
                ],
            ]],
            'success_url' => route('carpooling.bookings.payment-success', $booking) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('carpooling.bookings.payment-cancelled', $booking),
            'metadata' => [
                'carpool_booking_id' => (string) $booking->id,
            ],
        ]);

        CarpoolPayment::create([
            'carpool_booking_id' => $booking->id,
            'amount' => $booking->total_fare,
            'currency' => 'usd',
            'status' => CarpoolPayment::STATUS_PENDING,
            'gateway' => 'stripe',
            'stripe_checkout_session_id' => $session->id,
        ]);

        return $session;
    }
}
