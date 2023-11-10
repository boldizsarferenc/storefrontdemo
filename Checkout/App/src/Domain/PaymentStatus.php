<?php

namespace App\Domain;


class PaymentStatus
{
    public function __construct(
        private string $paymentStatus,
        private string $gatewayUrl
    ) {
    }

    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }

    public function getGatewayUrl(): string
    {
        return $this->gatewayUrl;
    }
}
