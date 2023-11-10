<?php

namespace App\Application\Service;

class PaymentRedirectUrlFactory
{

    public function createRedirectUrl(float $total, string $redirectUrl , string $paymentMethodName): ?string {

        if($paymentMethodName === "fakepayment"){
            return "http://localhost:8503/api/fakePayment?total=$total&redirectUrl=$redirectUrl";
        }

        return null;
    }
}
