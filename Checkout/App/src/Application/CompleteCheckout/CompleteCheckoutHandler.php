<?php

namespace App\Application\CompleteCheckout;

use App\Domain\Api\OrderApiInterface;
use App\Domain\Checkout;
use App\Domain\CheckoutRepositoryInterface;
use App\Domain\Saga\CheckoutSagaStepInterface;
use App\Domain\Saga\SagaLoggerInterface;
use App\Domain\Saga\WorkflowInterface;

class CompleteCheckoutHandler implements CheckoutSagaStepInterface
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly CheckoutRepositoryInterface $checkoutRepository,
        private readonly OrderApiInterface $orderApi,
        private readonly SagaLoggerInterface $logger
    ) {
    }

    public function execute(Checkout $checkout): ?string
    {
        if ($this->workflow->can($checkout, 'complete_checkout')) {
            $this->orderApi->createOrder($checkout->getCheckoutId());
            $this->workflow->apply($checkout, 'complete_checkout');
            $this->checkoutRepository->updateCheckout($checkout);
            $this->logger->debug('[CompleteCheckoutHandler] execute finished');
        }
        return null;
    }

    public function compensate(Checkout $checkout): void
    {
        // TODO: $this->orderApi->removeOrder($checkout->getCheckoutId());
        $this->logger->debug('[CompleteCheckoutHandler] compensate finished');
    }
}
