<?php

namespace App\Presentation\Api\Controller;

use App\Application\CreateShippingMethod\CreateShippingMethodCommand;
use App\Application\DeleteShippingMethod\DeleteShippingMethodCommand;
use App\Application\FetchShippingMethods\FetchShippingMethodsQuery;
use App\Application\GetShippingMethodById\GetShippingMethodByIdQuery;
use App\Application\IsValidShippingAddress\IsValidShippingAddressQuery;
use App\Domain\ShippingMethod;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class ShippingMethodsController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function fetchShippingMethods(Request $request): JsonResponse
    {
        $cartTotal = $request->get("cartTotal");

        return $this->json(
            $this->handle(
                new FetchShippingMethodsQuery($cartTotal)
            )
        );
    }

    public function getShippingMethodById(Request $request): JsonResponse
    {
        $shippingMethodId = $request->get("shippingMethodId");

        if (!$shippingMethodId) {
            return $this->json([
                "error" => "shippingMethodId is required"
            ]);
        }

        try {
            $response = $this->json(
                $this->handle(
                    new GetShippingMethodByIdQuery($shippingMethodId)
                )
            );
        } catch (\Throwable) {
            return new JsonResponse(["error"]);
        }
        return $response;
    }

    public function createShippingMethod(Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);

        /** @var ShippingMethod $created */
        $created = $this->handle(
            new CreateShippingMethodCommand(
                $requestBody["name"],
                $requestBody["description"],
                $requestBody["shippingLanes"],
                $requestBody["isEnabled"]
            )
        );

        return new JsonResponse(['id' => $created->getId()], 201);
    }

    public function isValidShippingAddress(Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);

        $isValid = $this->handle(
            new IsValidShippingAddressQuery(
                $requestBody["address"],
                $requestBody["country"],
                $requestBody["postcode"],
                $requestBody["city"]
            )
        );

        return new JsonResponse(['isValid' => $isValid], 200);
    }

    public function deleteShippingMethod(string $shippingMethodId): JsonResponse
    {
//        try {
//        } catch (\Throwable) {
////            return new JsonResponse(["error"]);
//        }
        $this->handle(
            new DeleteShippingMethodCommand($shippingMethodId)
        );

        return new JsonResponse([], 204);
    }
}
