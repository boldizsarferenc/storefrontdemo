<?php

namespace App\Presentation\Api\Controller;

use App\Application\GetPaymentByCheckout\GetPaymentByCheckoutIdHandler;
use App\Application\GetPaymentByCheckout\GetPaymentByCheckoutIdQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractController
{
    public function index(Request $request, GetPaymentByCheckoutIdHandler $getPaymentHandler
    ): JsonResponse
    {
        $checkoutId = $request->get('checkoutId');

        $payment = $getPaymentHandler->execute(
            new GetPaymentByCheckoutIdQuery($checkoutId)
        );

        return new JsonResponse(
            $payment
        );
    }
}
