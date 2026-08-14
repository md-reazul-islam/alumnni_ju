<?php

namespace App\Services\Payment;

class PaymentResult
{
    public function __construct(
        public readonly bool $successful,
        public readonly ?string $reference = null,
        public readonly ?string $errorMessage = null,
    ) {
    }
}
