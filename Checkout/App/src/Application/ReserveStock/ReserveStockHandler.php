<?php

namespace App\Application\ReserveStock;

use App\Domain\Catalog\CatalogAdapter;
use App\Domain\Checkout;
use App\Domain\CheckoutRepositoryInterface;
use App\Domain\Saga\CheckoutSagaStepInterface;
use App\Domain\Saga\SagaLoggerInterface;
use App\Domain\Saga\WorkflowInterface;

class ReserveStockHandler implements CheckoutSagaStepInterface
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly CheckoutRepositoryInterface $checkoutRepository,
        private readonly CatalogAdapter $catalogAdapter,
        private readonly SagaLoggerInterface $logger
    ) {
    }

    public function execute(Checkout $checkout): ?string
    {
        if ($this->workflow->can($checkout, 'reserve_stock')) {

            foreach ($checkout->getCart()->getCartItems() as $checkoutItem) {
                $this->catalogAdapter->subtractStock($checkoutItem->getSku(), $checkoutItem->getQuantity());
            }
            // throw new \Exception('The ordered product is out of stock!');
            $this->workflow->apply($checkout, 'reserve_stock');
            $this->checkoutRepository->updateCheckout($checkout);

            $this->logger->debug('[ReserveStockHandler] execute finished');
        }
        return null;
    }

    public function compensate(Checkout $checkout): void
    {
        foreach ($checkout->getCart()->getCartItems() as $checkoutItem) {
            $this->catalogAdapter->addStock($checkoutItem->getSku(), $checkoutItem->getQuantity());
        }
        $this->logger->debug('[ReserveStockHandler] compensate finished');
    }
}
