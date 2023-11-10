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
        if ($this->workflow->can($checkout, 'start_payment')) {
            $redirectUrl = $this->paymentApi->createPaymentMethod(
                $checkout->getCheckoutId(),
                $checkout->getCustomer(),
                $checkout->getCart()->getCartTotal()
            );

            $checkout->setGatewayUrl($paymentStatus->getGatewayUrl());

            $this->workflow->apply($checkout, 'start_payment');
            $this->checkoutRepository->updateCheckout($checkout);

            $this->logger->debug('[StartPaymentHandler] execute finished');

//            return 'http://localhost/checkout/'.$checkout->getCheckoutId().'/complete-payment';
            return $redirectUrl;
        }
        return null;
    }

    public function compensate(Checkout $checkout): void
    {
        $this->logger->debug('[StartPaymentHandler] compensate finished');
    }
}
