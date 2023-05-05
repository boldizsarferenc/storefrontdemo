<?php

namespace App\Application\GetPaymentQuery;

use App\Application\DTO\PaymentDTO;
use App\Application\Service\PaymentRedirectUrlFactory;
use App\Domain\PaymentId;
use App\Domain\PaymentRepositoryInterface;

class GetPaymentHandler
{
    private PaymentRepositoryInterface $paymentRepository;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
    ) {
        $this->paymentRepository = $paymentRepository;
    }

    public function execute(GetPaymentQuery $query): PaymentDTO
    {
        $payment = $this->paymentRepository->get(
            new PaymentId($query->getPaymentId())
        );

        $dto = new PaymentDTO();
        $dto->paymentId = $payment->getPaymentId()->getId();
        $dto->paymentMethodId = $payment->getPaymentMethodId()->getId();
        $dto->customer = $payment->getCustomer();
        $dto->amount = $payment->getAmount();
        $dto->status = $payment->getStatus();
        $dto->redirectUrl = $payment->getRedirectUrl();

        return $dto;
    }

}
