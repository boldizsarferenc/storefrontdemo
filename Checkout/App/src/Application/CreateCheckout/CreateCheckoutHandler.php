<?php

namespace App\Application\CreateCheckout;

use App\Domain\Api\CartApiInterface;
use App\Domain\Checkout;
use App\Domain\CheckoutRepositoryInterface;
use App\Domain\CheckoutStatus;
use App\Domain\Customer;
use App\Domain\Shared\EntityId;
use App\Domain\Shared\EntityIdGeneratorInterface;

class CreateCheckoutHandler
{
    public function __construct(
        private EntityIdGeneratorInterface $entityIdGenerator,
        private CheckoutRepositoryInterface $checkoutRepository,
        private CartApiInterface $cartApi
    ) {
    }

    public function __invoke(CreateCheckoutCommand $command): Checkout
    {
        $checkoutId = $this->entityIdGenerator->generate();
        $cart = $this->cartApi->getCart($command->cartId);
        $checkout = new Checkout(
            $checkoutId,
            CheckoutStatus::STATUS_PENDING,
            $cart
        );
        $this->checkoutRepository->createCheckout($checkout);
        return $checkout;
    }
}
