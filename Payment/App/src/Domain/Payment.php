<?php

namespace App\Domain;

class Payment
{
    private PaymentId $paymentId;

    private PaymentMethodId $paymentMethodId;

    private array $customer;

    private float $amount;

    /**
     * @TODO enumnak kellene lennie
     */
    private string $status;

    private ?string $redirectUrl;

    /**
     * @return PaymentId
     */
    public function getPaymentId(): PaymentId
    {
        return $this->paymentId;
    }

    /**
     * @param PaymentId $paymentId
     */
    public function setPaymentId(PaymentId $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return PaymentMethodId
     */
    public function getPaymentMethodId(): PaymentMethodId
    {
        return $this->paymentMethodId;
    }

    /**
     * @param PaymentMethodId $paymentMethodId
     */
    public function setPaymentMethodId(PaymentMethodId $paymentMethodId): void
    {
        $this->paymentMethodId = $paymentMethodId;
    }

    /**
     * @return array
     */
    public function getCustomer(): array
    {
        return $this->customer;
    }

    /**
     * @param array $customer
     */
    public function setCustomer(array $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(?string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }
}
