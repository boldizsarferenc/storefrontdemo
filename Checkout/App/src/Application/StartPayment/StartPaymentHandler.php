<?php

namespace App\Application\StartPayment;

use App\Domain\Api\PaymentApiInterface;
use App\Domain\Checkout;
use App\Domain\Saga\CheckoutSagaStepInterface;
use App\Domain\Saga\SagaLoggerInterface;
use App\Domain\CheckoutRepositoryInterface;
use App\Domain\Saga\WorkflowInterface;

class StartPaymentHandler implements CheckoutSagaStepInterface
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly CheckoutRepositoryInterface $checkoutRepository,
        private readonly PaymentApiInterface $paymentApi,
        private readonly SagaLoggerInterface $logger
    ) {
    }

    public function execute(Checkout $checkout): ?string
    {
        if ($this->workflow->can($checkout, $this->getTransactionName())) {
            $paymentStatus = $this->paymentApi->createPaymentMethod(
                $checkout->getCheckoutId(),
                $checkout->getPaymentMethod()->getExternalPaymentMethodId(),
                $checkout->getCustomer(),
                $checkout->getCart()->getCartTotal()
            );

            $checkout->setGatewayUrl($paymentStatus->getGatewayUrl());

            $this->workflow->apply($checkout, $this->getTransactionName());
            $this->checkoutRepository->updateCheckout($checkout);

            $this->logger->debug('[StartPaymentHandler] execute finished');

            return $paymentStatus->getGatewayUrl();
        }
        return null;
    }

    public function compensate(Checkout $checkout): void
    {
        $this->logger->debug('[StartPaymentHandler] compensate finished');
    }

    public function getTransactionName(): string
    {
        return 'start_payment';
    }
}
