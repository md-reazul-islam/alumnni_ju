<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    /**
     * Charge the given amount and return a transaction reference.
     * Implementations must never persist raw card data — only a gateway-issued token/reference.
     */
    public function charge(float $amount, string $currency, array $meta = []): PaymentResult;
}
