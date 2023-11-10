<?php

namespace App\Application\ConfirmPayment;

use App\Application\CheckoutOrchestrator\CheckoutSagaOrchestrator;
use App\Application\Exception\ApplicationException;
use App\Domain\Checkout;
use App\Domain\CheckoutRepositoryInterface;
use App\Domain\CheckoutStatus;
use App\Domain\Saga\SagaLoggerInterface;
use App\Domain\Shared\EntityId;
use Exception;

class ConfirmPaymentHandler
{
    public function __construct(
        private readonly CheckoutRepositoryInterface $checkoutRepository,
        private readonly CheckoutSagaOrchestrator $orchestrator,
        private readonly SagaLoggerInterface $sagaLogger
    ) {
    }

    /**
     * @throws ApplicationException
     */
    public function __invoke(ConfirmPaymentCommand $command): Checkout
    {
        $checkout = $this->checkoutRepository->findCheckout(new EntityId($command->checkoutId));

        if ($checkout === null) {
            throw new ApplicationException('checkout not found');
        }

        try {
            $this->orchestrator->execute($checkout);
        } catch (Exception $exception) {
            $errorMessage = sprintf('An error occurred while processing the order. Reason: %s', $exception->getMessage());
            $this->sagaLogger->debug(sprintf('[ConfirmPaymentCommand] %s', $errorMessage));
            $this->orchestrator->compensate($checkout);
            $checkout->setCheckoutStatus(CheckoutStatus::STATUS_FAILED);
            $this->checkoutRepository->updateCheckout($checkout);
            throw new ApplicationException($errorMessage, $exception->getCode(), $exception);
        }

        return $checkout;
    }
}
