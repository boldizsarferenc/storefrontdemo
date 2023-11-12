<?php

namespace App\WebshopBundle\Application\Checkout\ConfirmPayment;

use App\WebshopBundle\Application\Cart\Exception\ApplicationException;
use App\WebshopBundle\Application\Checkout\ConfirmCheckout\Dto\ConfirmCheckoutOutput;
use App\WebshopBundle\Application\Checkout\ConfirmCheckout\Dto\ConfirmPaymentOutput;
use App\WebshopBundle\Domain\Exception\DomainException;
use App\WebshopBundle\Domain\Model\Checkout\CheckoutRepositoryInterface;

class ConfirmPaymentHandler
{
    private CheckoutRepositoryInterface $checkoutRepository;

    public function __construct(CheckoutRepositoryInterface $checkoutRepositoryInterface)
    {
        $this->checkoutRepository = $checkoutRepositoryInterface;
    }

    public function __invoke(ConfirmPaymentCommand $command): ConfirmPaymentOutput
    {
        try {
            $checkout = $this->checkoutRepository->confirmPayment($command->getCheckoutId());
        } catch (DomainException $e) {
            throw new ApplicationException($e->getMessage());
        }

        return new ConfirmPaymentOutput($checkout);
    }
}
