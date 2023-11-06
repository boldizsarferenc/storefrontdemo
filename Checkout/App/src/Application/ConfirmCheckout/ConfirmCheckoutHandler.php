<?php

namespace App\Application\ConfirmCheckout;

use App\Application\Exception\ApplicationException;
use App\Domain\Api\OrderApiInterface;
use App\Domain\Api\PaymentApiInterface;
use App\Domain\Checkout;
use App\Domain\CheckoutRepositoryInterface;
use App\Domain\CheckoutStatus;
use App\Domain\Saga\CheckoutSagaOrchestrator;
use App\Domain\Saga\Steps\CheckoutCompletedStep;
use App\Domain\Saga\Steps\CheckoutPaidStep;
use App\Domain\Saga\Steps\CheckoutPaymentStep;
use App\Domain\Shared\EntityId;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class ConfirmCheckoutHandler
{
    public function __construct(
        private readonly CheckoutRepositoryInterface $checkoutRepository,
        private readonly PaymentApiInterface $paymentApi,
        private readonly OrderApiInterface $orderApi,
        private readonly WorkflowInterface $checkoutProcessWorkflow,
        private readonly LoggerInterface $sagaLogger
    ) {
    }

    /**
     * @param ConfirmCheckoutCommand $command
     * @return Checkout
     * @throws ApplicationException
     */
    public function __invoke(ConfirmCheckoutCommand $command): Checkout
    {
        $checkout = $this->checkoutRepository->findCheckout(new EntityId($command->checkoutId));

        if ($checkout === null) {
            throw new ApplicationException('checkout not found');
        }

        $sagaSteps = [
            new CheckoutPaymentStep(
                $this->checkoutProcessWorkflow,
                $this->checkoutRepository,
                $this->paymentApi,
                $this->sagaLogger
            ),
            new CheckoutPaidStep(
                $this->checkoutProcessWorkflow,
                $this->checkoutRepository,
                $this->sagaLogger
            ),
            new CheckoutCompletedStep(
                $this->checkoutProcessWorkflow,
                $this->checkoutRepository,
                $this->orderApi,
                $this->sagaLogger
            )
        ];
        $saga = new CheckoutSagaOrchestrator($sagaSteps);

        try {
            $saga->execute($checkout);
        } catch (Exception $exception) {
            $errorMessage = sprintf('An error occurred while processing the order. Reason: %s', $exception->getMessage());
            $this->sagaLogger->debug(sprintf('[ConfirmCheckoutHandler] %s', $errorMessage));

            $saga->compensate($checkout);

            $checkout->setCheckoutStatus(CheckoutStatus::STATUS_FAILED);
            $this->checkoutRepository->updateCheckout($checkout);

            throw new ApplicationException($errorMessage, $exception->getCode(), $exception);
        }

        return $checkout;
    }
}
