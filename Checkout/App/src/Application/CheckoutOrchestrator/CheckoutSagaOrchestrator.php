<?php

namespace App\Application\CheckoutOrchestrator;

use App\Application\CheckShipping\CheckShippingHandler;
use App\Application\CheckStatus\CheckStatusHandler;
use App\Application\CompleteCheckout\CompleteCheckoutHandler;
use App\Application\CompletePayment\CompletePaymentHandler;
use App\Application\ReserveStock\ReserveStockHandler;
use App\Application\StartPayment\StartPaymentHandler;
use App\Domain\Checkout;
use App\Domain\Saga\CheckoutSagaStepInterface;
use App\Domain\Saga\WorkflowInterface;

class CheckoutSagaOrchestrator
{
    /**
     * @var CheckoutSagaStepInterface[]
     */
    private array $steps;

    public function __construct(
        private readonly WorkflowInterface $workflow,
        ReserveStockHandler $reserveStock,
        StartPaymentHandler $startPayment,
        CompletePaymentHandler $completePayment,
        CheckStatusHandler $checkStatus,
        CheckShippingHandler $checkShipping,
        CompleteCheckoutHandler $completeCheckout,
    ) {
        $this->steps = [$reserveStock, $startPayment, $completePayment, $checkStatus, $checkShipping, $completeCheckout];
    }

    public function execute(Checkout $checkout): ?string
    {
        foreach ($this->steps as $step) {
            $result = $step->execute($checkout);
            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }

    public function compensate(Checkout $checkout): void
    {
        $currentStepFound = false;
        foreach (array_reverse($this->steps) as $step) {
            if (!$currentStepFound && !$this->workflow->can($checkout, $step->getTransactionName())) {
                continue;
            }
            if (!$currentStepFound) {
                $currentStepFound = true;
                continue;
            }
            $step->compensate($checkout);
        }
    }
}
