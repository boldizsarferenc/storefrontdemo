<?php

namespace App\Presentation\Api\Controller;

use App\Application\CreatePayment\CreatePaymentCommand;
use App\Application\CreatePayment\CreatePaymentHandler;
use App\Application\GetPaymentQuery\GetPaymentHandler;
use App\Application\GetPaymentQuery\GetPaymentQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Ramsey\Uuid\Uuid;

class InitiatePaymentController extends AbstractController
{
    public function index (
        Request $request,
        CreatePaymentHandler $createPaymentHandler,
        GetPaymentHandler $getPaymentHandler
    ): JsonResponse
    {
        $payload = json_decode(
            $request->getContent(),
            true
        );

        $paymentId = Uuid::uuid4()->toString();

        $createPaymentHandler->execute(
            new CreatePaymentCommand(
                $paymentId,
                $payload['checkoutId'],
                $payload['paymentMethodId'],
                $payload['customer'],
                (float)$payload['amount'],
                'SUCCESS'
            )
        );

        $payment = $getPaymentHandler->execute(
            new GetPaymentQuery($paymentId)
        );

        return new JsonResponse(
            $payment
        );
    }

}
