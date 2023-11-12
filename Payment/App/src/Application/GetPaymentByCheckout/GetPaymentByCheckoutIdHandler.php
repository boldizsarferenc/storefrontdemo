<?php

namespace App\Application\GetPaymentByCheckout;

use App\Application\DTO\PaymentDTO;
use App\Domain\PaymentRepositoryInterface;

class GetPaymentByCheckoutIdHandler
{
    private PaymentRepositoryInterface $paymentRepository;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
    ) {
        $this->paymentRepository = $paymentRepository;
    }

    public function execute(GetPaymentByCheckoutIdQuery $query): PaymentDTO
    {
        $payment = $this->paymentRepository->getByCheckoutId(
           $query->getCheckoutId()
        );

        $dto = new PaymentDTO();
        $dto->id = $payment->getPaymentId()->getId();
        $dto->paymentMethodId = $payment->getPaymentMethodId()->getId();
        $dto->checkoutId = $payment->getCheckoutId();
        $dto->customer = $payment->getCustomer();
        $dto->amount = $payment->getAmount();
        $dto->status = $payment->getStatus();
        $dto->redirectUrl = $payment->getRedirectUrl();

        return $dto;
    }
}
