<?php

namespace App\Application\CheckShipping;

use App\Domain\Api\ShippingApiInterface;
use App\Domain\Checkout;
use App\Domain\CheckoutRepositoryInterface;
use App\Domain\Saga\CheckoutSagaStepInterface;
use App\Domain\Saga\SagaLoggerInterface;
use App\Domain\Saga\WorkflowInterface;

class CheckShippingHandler implements CheckoutSagaStepInterface
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly CheckoutRepositoryInterface $checkoutRepository,
        private readonly ShippingApiInterface $shippingApi,
        private readonly SagaLoggerInterface $logger
    ) {
    }

    public function execute(Checkout $checkout): ?string
    {
        if ($this->workflow->can($checkout, $this->getTransactionName())) {
            $isValid = $this->shippingApi->isValidAddress($checkout);
            if(!$isValid) {
                throw new \Exception('Shipping address is not valid!');
            }
            $this->workflow->apply($checkout, $this->getTransactionName());
            $this->checkoutRepository->updateCheckout($checkout);
            $this->logger->debug('[CheckShippingHandler] execute finished');
        }
        return null;
    }

    public function compensate(Checkout $checkout): void
    {
        $this->logger->debug('[CheckShippingHandler] compensate finished');
    }

    public function getTransactionName(): string
    {
        return 'check_shipping';
    }
}
