<?php

namespace App\WebshopBundle\Application\Checkout\ConfirmPayment;

class ConfirmPaymentCommand
{

    public function __construct(
        private string $checkoutId,
    )
    {
    }

    public function getCheckoutId(): string
    {
        return $this->checkoutId;
    }
}
