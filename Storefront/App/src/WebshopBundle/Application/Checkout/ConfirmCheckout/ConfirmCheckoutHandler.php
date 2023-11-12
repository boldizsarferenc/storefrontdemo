<?php

namespace App\WebshopBundle\Application\Checkout\ConfirmCheckout;

use App\WebshopBundle\Application\Cart\Exception\ApplicationException;
use App\WebshopBundle\Application\Checkout\ConfirmCheckout\Dto\ConfirmCheckoutOutput;
use App\WebshopBundle\Domain\Exception\DomainException;
use App\WebshopBundle\Domain\Model\Checkout\CheckoutRepositoryInterface;

class ConfirmCheckoutHandler
{
    private CheckoutRepositoryInterface $checkoutRepository;

    public function __construct(CheckoutRepositoryInterface $checkoutRepositoryInterface)
    {
        $this->checkoutRepository = $checkoutRepositoryInterface;
    }

    public function __invoke(ConfirmCheckoutCommand $command): array
    {
        try {
            return $this->checkoutRepository->confirmCheckout($command->getCheckoutId());
        } catch (DomainException $e) {
            throw new ApplicationException($e->getMessage());
        }

        return [];
    }
}
