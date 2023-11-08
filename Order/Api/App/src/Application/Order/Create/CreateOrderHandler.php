<?php

namespace App\Application\Order\Create;

use App\Domain\Checkout\CheckoutAdapterInterface;
use App\Domain\Order\Order;
use App\Domain\Order\OrderFactory;
use App\Domain\Order\OrderRepositoryInterface;
use App\Domain\Catalog\CatalogAdapter;
use Throwable;

class CreateOrderHandler
{
    public function __construct(
            private readonly OrderRepositoryInterface $orderRepository,
            private readonly CheckoutAdapterInterface $checkoutAdapter,
            private readonly CatalogAdapter $catalogAdapter,
            private readonly OrderFactory $orderFactory,
    ) {
    }

    public function __invoke(CreateOrderCommand $createOrderCommand): Order
    {
        $checkout = $this->checkoutAdapter->getCheckoutById($createOrderCommand->getCheckoutId());

        if (strtoupper($checkout->getStatus()) !== 'COMPLETED') {
            throw new CreateOrderException('checkout status is not COMPLETED');
        }

        $stockReducedItems = [];
        try {
            foreach ($checkout->getCart()->getItems() as $checkoutItem) {
                $this->catalogAdapter->subtractStock($checkoutItem->getSku(), $checkoutItem->getQuantity());
                $stockReducedItems[] = $checkoutItem;
            }

            $order = $this->orderFactory->createOrderFromCheckout($checkout);
            $order = $this->orderRepository->add($order);

            // todo: make it orderOutput
            return $order;
        } catch (Throwable $throwable) {
            foreach ($stockReducedItems as $item) {
                $this->catalogAdapter->addStock($item->getSku(), $item->getQuantity());
            }

            throw new CreateOrderException($throwable->getMessage());
        }
    }
}
