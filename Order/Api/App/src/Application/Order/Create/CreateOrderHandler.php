<?php

namespace App\Application\Order\Create;

use App\Domain\Checkout\CheckoutAdapterInterface;
use App\Domain\Order\Order;
use App\Domain\Order\OrderFactory;
use App\Domain\Order\OrderRepositoryInterface;
use Throwable;

class CreateOrderHandler
{
    public function __construct(
            private readonly OrderRepositoryInterface $orderRepository,
            private readonly CheckoutAdapterInterface $checkoutAdapter,
            private readonly OrderFactory $orderFactory
    ) {
    }

    public function __invoke(CreateOrderCommand $createOrderCommand): Order
    {
        $checkout = $this->checkoutAdapter->getCheckoutById($createOrderCommand->getCheckoutId());

        if (strtoupper($checkout->getStatus()) !== 'SHIPPING_CHECKED') {
            throw new CreateOrderException('checkout status is not SHIPPING_CHECKED');
        }

        try {
            $order = $this->orderFactory->createOrderFromCheckout($checkout);
            $order = $this->orderRepository->add($order);

            // todo: make it orderOutput
            return $order;
        } catch (Throwable $throwable) {
            throw new CreateOrderException($throwable->getMessage());
        }
    }
}
