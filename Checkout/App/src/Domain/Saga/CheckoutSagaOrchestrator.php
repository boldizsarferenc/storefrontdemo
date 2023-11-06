<?php

namespace App\Domain\Saga;

use App\Domain\Checkout;

class CheckoutSagaOrchestrator
{
    public function __construct(private readonly array $steps) {
    }

    public function execute(Checkout $checkout): void
    {
        foreach ($this->steps as $step) {
            $step->execute($checkout);
        }
    }

    public function compensate(Checkout $checkout): void
    {
        foreach (array_reverse($this->steps) as $step) {
            $step->compensate($checkout);
        }
    }
}
