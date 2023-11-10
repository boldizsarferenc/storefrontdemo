<?php

namespace App\Application\GetPaymentByCheckout;

class GetPaymentByCheckoutIdQuery
{
    private string $checkoutId;

    public function __construct(string $checkoutId) {
        $this->checkoutId = $checkoutId;
    }

    public function getCheckoutId(): string {
        return $this->checkoutId;
    }
}
