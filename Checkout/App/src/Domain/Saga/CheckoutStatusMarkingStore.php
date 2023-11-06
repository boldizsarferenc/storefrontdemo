<?php

namespace App\Domain\Saga;

use App\Domain\Checkout;
use App\Domain\CheckoutStatus;
use App\Domain\DomainException;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class CheckoutStatusMarkingStore implements MarkingStoreInterface
{
    /**
     * @throws DomainException
     */
    public function getMarking(object $subject): Marking
    {
        if (!($subject instanceof Checkout)) {
            throw new DomainException('Invalid subject type.');
        }

        return new Marking([$subject->getCheckoutStatus()->value => 1]);
    }

    /**
     * @throws DomainException
     */
    public function setMarking(object $subject, Marking $marking, array $context = []): void
    {
        if (!($subject instanceof Checkout)) {
            throw new DomainException('Invalid subject type.');
        }

        $markingPlaces = $marking->getPlaces();
        $markingValue = key($markingPlaces);
        $subject->setCheckoutStatus(CheckoutStatus::fromValue($markingValue));
    }
}
