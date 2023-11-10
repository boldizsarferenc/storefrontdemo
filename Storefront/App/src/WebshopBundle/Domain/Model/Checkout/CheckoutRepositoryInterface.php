<?php

namespace App\WebshopBundle\Domain\Model\Checkout;

use App\WebshopBundle\Domain\Exception\DomainException;
use App\WebshopBundle\Domain\Model\Checkout\Dto\Address;
use App\WebshopBundle\Domain\Model\Checkout\Dto\Customer;

interface CheckoutRepositoryInterface
{
    public function createCheckout(string $cartId): Checkout;
    public function addCustomer(string $checkoutId, Customer $customer): Checkout;
    public function addShippingAddress(string $checkoutId, Address $address): Checkout;
    public function addBillingAddress(string $checkoutId, Address $address): Checkout;
    public function addShippingMethod(string $checkoutId, string $shippingMethodId): Checkout;
    public function addPaymentMethod(string $checkoutId, string $paymentMethodId): Checkout;

    /**
     * @throws DomainException
     */
    public function confirmCheckout(string $checkoutId): array;
    /**
     * @throws DomainException
     */
    public function confirmPayment(string $checkoutId): Checkout;
    /**
     * @throws DomainException
     */
    public function getCheckout(string $checkoutId): ?Checkout;

}
