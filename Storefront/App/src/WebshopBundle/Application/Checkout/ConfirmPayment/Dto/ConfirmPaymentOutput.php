<?php

namespace App\WebshopBundle\Application\Checkout\ConfirmCheckout\Dto;

use App\WebshopBundle\Domain\Model\Checkout\Checkout;
use JsonSerializable;
use function get_object_vars;

class ConfirmPaymentOutput implements JsonSerializable
{
    private Checkout $checkout;

    public function __construct(Checkout $checkout)
    {
        $this->checkout = $checkout;
    }

    public function getCheckout(): Checkout
    {
        return $this->checkout;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
