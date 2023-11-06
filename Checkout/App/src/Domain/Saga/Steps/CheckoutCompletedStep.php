<?php

namespace App\Domain\Saga\Steps;

use App\Domain\Api\OrderApiInterface;
use App\Domain\Checkout;
use App\Domain\CheckoutRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class CheckoutCompletedStep
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly CheckoutRepositoryInterface $checkoutRepository,
        private readonly OrderApiInterface $orderApi,
        private readonly LoggerInterface $sagaLogger
    ) {
    }

    public function execute(Checkout $checkout): void
    {
        if ($this->workflow->can($checkout, 'to_completed')) {
            $this->workflow->apply($checkout, 'to_completed');
            $this->checkoutRepository->updateCheckout($checkout);

            $this->orderApi->createOrder($checkout->getCheckoutId());
            $this->sagaLogger->debug('[CheckoutCompletedStep] execute finished');
        }
    }

    public function compensate(Checkout $checkout): void
    {
        // TODO: $this->orderApi->removeOrder($checkout->getCheckoutId());
        $this->sagaLogger->debug('[CheckoutCompletedStep] compensate finished');
    }
}
