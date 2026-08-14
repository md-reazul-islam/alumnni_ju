<?php

namespace App\Services\Payment;

use Illuminate\Support\Str;

/**
 * Default gateway used until a real processor (e.g. Stripe) is wired in.
 * Swap the App\Services\Payment\PaymentGatewayInterface binding in AppServiceProvider
 * to point at a StripePaymentGateway implementing the same interface — no controller
 * or model changes required.
 */
class ManualPaymentGateway implements PaymentGatewayInterface
{
    public function charge(float $amount, string $currency, array $meta = []): PaymentResult
    {
        return new PaymentResult(
            successful: true,
            reference: 'MANUAL-' . strtoupper(Str::random(12)),
        );
    }
}
