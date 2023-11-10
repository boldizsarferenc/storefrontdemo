<?php

namespace App\Domain\Api;

use App\Domain\Customer;
use App\Domain\PaymentMethod;
use App\Domain\PaymentStatus;

interface PaymentApiInterface
{
    public function getPaymentMethod(string $externalPaymentMethodId): PaymentMethod;
    public function createPaymentMethod(string $externalPaymentMethodId, Customer $customer, float $cartTotal): ?string;
    public function getPaymentStatus(string $externalPaymentMethodId): PaymentStatus;
}
