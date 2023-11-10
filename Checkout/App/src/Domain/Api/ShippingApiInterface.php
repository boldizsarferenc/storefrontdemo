<?php

namespace App\Domain\Api;

use App\Domain\Checkout;
use App\Domain\ShippingMethod;

interface ShippingApiInterface
{
    public function getShippingMethod(string $externalShippingMethodId): ShippingMethod;

    public function isValidAddress(Checkout $checkout): bool;
}
