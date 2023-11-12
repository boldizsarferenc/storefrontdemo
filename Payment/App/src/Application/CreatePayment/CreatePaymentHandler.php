<?php

namespace App\Application\CreatePayment;

use App\Application\Service\PaymentRedirectUrlFactory;
use App\Domain\Payment;
use App\Domain\PaymentId;
use App\Domain\PaymentMethodId;
use App\Domain\PaymentRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Repository\PaymentMethodRepository;

class CreatePaymentHandler
{
    private PaymentRepositoryInterface $paymentRepository;

    private PaymentRedirectUrlFactory $redirectUrlFactory;
    private PaymentMethodRepository $paymentMethodRepository;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        PaymentRedirectUrlFactory $redirectUrlFactory,
        PaymentMethodRepository $repository,
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->redirectUrlFactory = $redirectUrlFactory;
        $this->paymentMethodRepository = $repository;
    }

    public function execute(CreatePaymentCommand $command) {
        $payment = new Payment();

        $payment->setPaymentId(
            new PaymentId($command->getPaymentId())
        );

        $paymentMethod = $this->paymentMethodRepository->find($command->getPaymentMethodId());


        $payment->setPaymentMethodId(
            new PaymentMethodId($command->getPaymentMethodId())
        );

        $payment->setCheckoutId($command->getCheckoutId());

        $payment->setAmount($command->getAmount());
        $payment->setCustomer($command->getCustomer());
        $payment->setStatus($paymentMethod->getName() === 'fakePayment' ? 'PENDING' : 'SUCCESS');
//        "http://localhost/checkout/".$command->getCheckoutId()."/complete-payment"
        $payment->setRedirectUrl(
            $this->redirectUrlFactory->createRedirectUrl($command->getAmount(), $command->getPaymentId(), $paymentMethod->getName())
        );

        $this->paymentRepository->add($payment);
    }
}
