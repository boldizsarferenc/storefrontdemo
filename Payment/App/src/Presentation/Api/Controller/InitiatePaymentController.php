<?php

namespace App\Presentation\Api\Controller;

use App\Application\CreatePayment\CreatePaymentCommand;
use App\Application\CreatePayment\CreatePaymentHandler;
use App\Application\GetPaymentQuery\GetPaymentHandler;
use App\Application\GetPaymentQuery\GetPaymentQuery;
use App\Application\UpdatePayment\UpdatePaymentCommand;
use App\Application\UpdatePayment\UpdatePaymentHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    public function paymentRedirect
    (
        Request $request,
        UpdatePaymentHandler $updatePaymentHandler,
        GetPaymentHandler $getPaymentHandler
    ): RedirectResponse
    {
        $paymentId = $request->get("paymentId");
        $status = $request->get("status");


        $updatePaymentHandler->execute(
            new UpdatePaymentCommand(
                $paymentId,
                $status
            )
        );

        $payment = $getPaymentHandler->execute(
            new GetPaymentQuery($paymentId)
        );

        return $this->redirect("http://localhost/checkout/".$payment->checkoutId."/complete-payment");
    }

}
