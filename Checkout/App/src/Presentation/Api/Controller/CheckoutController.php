<?php

namespace App\Presentation\Api\Controller;

use App\Application\ConfirmCheckout\ConfirmCheckoutCommand;
use App\Application\ConfirmPayment\ConfirmPaymentCommand;
use App\Application\CreateCheckout\CreateCheckoutCommand;
use App\Application\GetCheckout\GetCheckoutQuery;
use App\Application\SaveBillingAddress\SaveBillingAddressCommand;
use App\Application\SaveCustomer\SaveCustomerCommand;
use App\Application\SavePaymentMethod\SavePaymentMethodCommand;
use App\Application\SaveShippingAddress\SaveShippingAddressCommand;
use App\Application\SaveShippingMethod\SaveShippingMethodCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CheckoutController extends AbstractController
{
    use HandleTrait;
    public function __construct(MessageBusInterface $messageBus,SerializerInterface $serializer)
    {
        $this->messageBus = $messageBus;
        $this->serializer = $serializer;
    }

    public function createCheckout(Request $request)
    {
        $post = json_decode($request->getContent());
        $command = new CreateCheckoutCommand($post->cartId);

        $response = $this->handle(
            $command
        );

        $jsonresponse = $this->serializer->serialize($response,'json');
        return new Response($jsonresponse);
    }

    public function getCheckout(string $checkoutId)
    {
        $query = new GetCheckoutQuery($checkoutId);

        $response = $this->handle(
            $query
        );

        $jsonresponse = $this->serializer->serialize($response,'json');
        return new Response($jsonresponse);
    }

    public function saveCustomer(string $checkoutId, Request $request)
    {
        $post = json_decode($request->getContent());
        $command = new SaveCustomerCommand(
            $checkoutId,
            $post->email,
            $post->lastName,
            $post->firstName,
            $post->phone
        );

        $response = $this->handle(
            $command
        );

        $jsonresponse = $this->serializer->serialize($response,'json');
        return new Response($jsonresponse);
    }

    public function saveShippingAddress(string $checkoutId, Request $request)
    {
        $post = json_decode($request->getContent());
        $command = new SaveShippingAddressCommand(
            $checkoutId,
            $post->address,
            $post->country,
            $post->postcode,
            $post->city
        );

        $response = $this->handle(
            $command
        );

        $jsonresponse = $this->serializer->serialize($response,'json');
        return new Response($jsonresponse);
    }

    public function saveBillingAddress(string $checkoutId, Request $request)
    {
        $post = json_decode($request->getContent());
        $command = new SaveBillingAddressCommand(
            $checkoutId,
            $post->address,
            $post->country,
            $post->postcode,
            $post->city
        );

        $response = $this->handle(
            $command
        );

        $jsonresponse = $this->serializer->serialize($response,'json');
        return new Response($jsonresponse);
    }

    public function savePaymentMethod(string $checkoutId, Request $request)
    {
        $post = json_decode($request->getContent());
        $command = new SavePaymentMethodCommand(
            $checkoutId,
            $post->paymentMethodId
        );

        $response = $this->handle(
            $command
        );

        $jsonresponse = $this->serializer->serialize($response,'json');
        return new Response($jsonresponse);
    }

    public function saveShippingMethod(string $checkoutId, Request $request)
    {
        $post = json_decode($request->getContent());
        $command = new SaveShippingMethodCommand(
            $checkoutId,
            $post->shippingMethodId
        );

        $response = $this->handle(
            $command
        );

        $jsonresponse = $this->serializer->serialize($response,'json');
        return new Response($jsonresponse);
    }

    public function confirm(string $checkoutId, Request $request)
    {
        $post = json_decode($request->getContent());
        $command = new ConfirmCheckoutCommand($checkoutId);
        $redirectUrl = $this->handle($command);

        return new JsonResponse(['redirectUrl' => $redirectUrl]);
    }

    public function completePayment(string $checkoutId, Request $request)
    {
        $post = json_decode($request->getContent());
        $command = new ConfirmPaymentCommand($checkoutId);
        $response = $this->handle($command);

        return new Response($this->serializer->serialize($response,'json'));
    }
}
