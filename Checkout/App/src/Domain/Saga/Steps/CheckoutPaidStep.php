<?php

namespace App\Domain\Saga\Steps;

use App\Domain\Checkout;
use App\Domain\CheckoutRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class CheckoutPaidStep
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly CheckoutRepositoryInterface $checkoutRepository,
        private readonly LoggerInterface $sagaLogger
    ) {
    }

    public function execute(Checkout $checkout): void
    {
        if ($this->workflow->can($checkout, 'to_paid')) {
            $this->workflow->apply($checkout, 'to_paid');
            $this->checkoutRepository->updateCheckout($checkout);

            // throw new \Exception('The ordered product is out of stock!');
            // TODO: $this->stockService->prepareOrder($checkout->getCheckoutId());
            $this->sagaLogger->debug('[CheckoutPaidStep] execute finished');
        }
    }

    public function compensate(Checkout $checkout): void
    {
        // TODO: $this->stockService->restoreOrder($checkout->getCheckoutId());
        $this->sagaLogger->debug('[CheckoutPaidStep] compensate finished');
    }
}
