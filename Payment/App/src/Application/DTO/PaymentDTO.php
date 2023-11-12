<?php

namespace App\Application\DTO;

class PaymentDTO
{
    public string $id;

    public string $paymentMethodId;

    public string $checkoutId;

    public array $customer;

    public float $amount;

    public string $status;

    public ?string $redirectUrl;
}
