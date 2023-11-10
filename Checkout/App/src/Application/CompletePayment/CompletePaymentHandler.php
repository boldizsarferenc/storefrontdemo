<?php

namespace App\Application\CompletePayment;

use App\Domain\Api\PaymentApiInterface;
use App\Domain\Checkout;
use App\Domain\CheckoutRepositoryInterface;
use App\Domain\Saga\CheckoutSagaStepInterface;
use App\Domain\Saga\SagaLoggerInterface;
use App\Domain\Saga\WorkflowInterface;

class CompletePaymentHandler implements CheckoutSagaStepInterface
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
        if ($this->workflow->can($checkout, 'complete_payment')) {
            $paymentStatus = $this->paymentApi->getPaymentStatus($checkout->getCheckoutId());

            if (strtoupper($paymentStatus->getPaymentStatus()) !== 'SUCCESS') {
                $errorMessage = sprintf(
                    'Current payment status is not SUCCESS. Current status is: %s',
                    strtoupper($paymentStatus->getPaymentStatus())
                );
                throw new \Exception($errorMessage); //TODO: rendberakni
            }

            $this->workflow->apply($checkout, 'complete_payment');
            $this->checkoutRepository->updateCheckout($checkout);
            $this->logger->debug('[CompletePaymentHandler] execute finished');
        }
        return null;
    }

    public function compensate(Checkout $checkout): void
    {
        // TODO: $this->paymentApi->refundPayment($checkout->getCheckoutId())
        $this->logger->debug('[CompletePaymentHandler] compensate finished');
    }
}
