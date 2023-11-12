<?php

namespace App\Application\ConfirmPayment;

class ConfirmPaymentCommand
{
    public function __construct(
        public readonly string $checkoutId
    ) {}
}
