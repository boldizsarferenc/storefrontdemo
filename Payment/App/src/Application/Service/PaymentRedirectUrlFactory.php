<?php

namespace App\Application\Service;

class PaymentRedirectUrlFactory
{

    public function createRedirectUrl(float $total, string $paymentId , string $paymentMethodName): ?string {

        if($paymentMethodName === "fakepayment"){
            return "http://localhost:8503/api/fakePayment?total=$total&paymentId=$paymentId";
        }

        return null;
    }
}
