<?php

namespace App\Presentation\Api\Controller;

use App\Application\GetPaymentMethod\GetPaymentMethodHandler;
use App\Application\GetPaymentMethod\GetPaymentMethodQuery;
use App\Application\GetPaymentQuery\GetPaymentQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentMethodController extends AbstractController
{
    public function index($paymentMethodId, GetPaymentMethodHandler $handler): JsonResponse
    {
        $paymentMethod = $handler->handle(new GetPaymentMethodQuery($paymentMethodId));

        return new JsonResponse([
            'id' => $paymentMethod->getId()->getId(),
            'name' => $paymentMethod->getName(),
            'description' => $paymentMethod->getDescription(),
            'fee' => $paymentMethod->getFee(),
            'isEnabled' => $paymentMethod->isEnabled()
        ]);
    }
}
