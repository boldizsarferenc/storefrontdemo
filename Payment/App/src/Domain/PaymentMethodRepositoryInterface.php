<?php

namespace App\Domain;

use App\Domain\PaymentMethodId;
use App\Domain\PaymentMethod;

interface PaymentMethodRepositoryInterface
{
    public function getPaymentMethod(string $id): PaymentMethod;

}
