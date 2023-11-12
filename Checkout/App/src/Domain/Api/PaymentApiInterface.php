<?php

namespace App\Domain\Api;

use App\Domain\Customer;
use App\Domain\PaymentMethod;
use App\Domain\PaymentStatus;

interface PaymentApiInterface
{
    public function getPaymentMethod(string $externalPaymentMethodId): PaymentMethod;
    public function createPaymentMethod(string $checkoutId, string $externalPaymentMethodId, Customer $customer, float $cartTotal): PaymentStatus;
    public function getPaymentStatus(string $checkoutId): PaymentStatus;
    public function refund(string $checkoutId): bool;
}
