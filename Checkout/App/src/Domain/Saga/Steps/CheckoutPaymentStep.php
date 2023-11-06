<?php

namespace App\Domain\Saga\Steps;

use App\Domain\Api\PaymentApiInterface;
use App\Domain\Checkout;
use App\Domain\CheckoutRepositoryInterface;
use App\Domain\Saga\SagaException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class CheckoutPaymentStep
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly CheckoutRepositoryInterface $checkoutRepository,
        private readonly PaymentApiInterface $paymentApi,
        private readonly LoggerInterface $sagaLogger
    ) {
    }

    /**
     * @throws SagaException
     */
    public function execute(Checkout $checkout): void
    {
        if ($this->workflow->can($checkout, 'to_unpaid')) {
            $this->workflow->apply($checkout, 'to_unpaid');
            $this->checkoutRepository->updateCheckout($checkout);

            $paymentStatus = $this->paymentApi->createPaymentMethod(
                $checkout->getCheckoutId(),
                $checkout->getCustomer(),
                $checkout->getCart()->getCartTotal()
            );

            if (strtoupper($paymentStatus->getPaymentStatus()) !== 'SUCCESS') {
                $errorMessage = sprintf(
                    'Current payment status is not SUCCESS. Current status is: %s',
                    strtoupper($paymentStatus->getPaymentStatus())
                );
                throw new SagaException($errorMessage);
            }
            $this->sagaLogger->debug('[CheckoutPaymentStep] execute finished');
        }
    }

    public function compensate(Checkout $checkout): void
    {
        // TODO: $this->paymentApi->refundPayment($checkout->getCheckoutId())
        $this->sagaLogger->debug('[CheckoutPaymentStep] compensate finished');
    }
}
