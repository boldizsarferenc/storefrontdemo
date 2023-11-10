<?php

namespace App\Application\UpdatePayment;

use App\Domain\PaymentId;
use App\Domain\PaymentRepositoryInterface;

class UpdatePaymentHandler
{


    public function __construct(
        private PaymentRepositoryInterface $repository
    )
    {
    }

    public function execute(UpdatePaymentCommand $paymentCommand)
    {
        $payment = $this->repository->get(new PaymentId($paymentCommand->paymentId));

        $payment->setStatus($paymentCommand->status ? "SUCCESS" : "FAILURE");

        $this->repository->save();


    }
}
