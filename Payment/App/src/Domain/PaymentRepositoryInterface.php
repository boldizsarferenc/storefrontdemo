<?php

namespace App\Domain;

interface PaymentRepositoryInterface
{
    public function get(PaymentId $id): Payment;

    public function getByCheckoutId(string $checkoutId): Payment;

    public function add(Payment $payment);

    public function save(): void;
}
