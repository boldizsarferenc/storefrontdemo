<?php

namespace App\Application\UpdatePayment;

class UpdatePaymentCommand
{

    public function __construct(
        public string $paymentId,
        public string $status
    )
    {
    }
}
