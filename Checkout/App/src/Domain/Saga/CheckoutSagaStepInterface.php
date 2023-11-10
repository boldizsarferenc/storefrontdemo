<?php

namespace App\Domain\Saga;

use App\Domain\Checkout;

interface CheckoutSagaStepInterface
{
    public function execute(Checkout $checkout): ?string;

    public function compensate(Checkout $checkout): void;
}
