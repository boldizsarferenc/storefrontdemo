<?php

namespace App\Application\CheckStatus;

use App\Domain\Api\PaymentApiInterface;
use App\Domain\Checkout;
use App\Domain\CheckoutRepositoryInterface;
use App\Domain\Saga\CheckoutSagaStepInterface;
use App\Domain\Saga\SagaLoggerInterface;
use App\Domain\Saga\WorkflowInterface;

class CheckStatusHandler implements CheckoutSagaStepInterface
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly CheckoutRepositoryInterface $checkoutRepository,
//        private readonly CatalogApi $catalogApi,
        private readonly SagaLoggerInterface $logger
    ) {
    }

    public function execute(Checkout $checkout): ?string
    {
        if ($this->workflow->can($checkout, 'check_status')) {
//            $paymentStatus = $this->catalogApi->isEnabledProduct($checkout->getCheckoutId());

            $this->workflow->apply($checkout, 'check_status');
            $this->checkoutRepository->updateCheckout($checkout);
            $this->logger->debug('[CheckStatusHandler] execute finished');
        }
        return null;
    }

    public function compensate(Checkout $checkout): void
    {
        $this->logger->debug('[CheckStatusHandler] compensate finished');
    }
}
