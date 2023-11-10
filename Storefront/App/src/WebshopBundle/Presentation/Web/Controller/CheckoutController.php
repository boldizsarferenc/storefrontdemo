<?php

namespace App\WebshopBundle\Presentation\Web\Controller;

use App\WebshopBundle\Application\Checkout\AddBillingAddress\AddBillingAddressCommand;
use App\WebshopBundle\Application\Checkout\AddShippingAddress\AddShippingAddressCommand;
use App\WebshopBundle\Application\Checkout\ConfirmCheckout\ConfirmCheckoutCommand;
use App\WebshopBundle\Application\Checkout\ConfirmPayment\ConfirmPaymentCommand;
use App\WebshopBundle\Application\Checkout\CreateCheckout\CreateCheckoutQuery;
use App\WebshopBundle\Application\Checkout\CreateCheckout\Dto\CreateCheckoutOutput;
use App\WebshopBundle\Application\Checkout\Customer\CreateCustomerCommand;
use App\WebshopBundle\Application\Checkout\GetCheckout\GetCheckoutQuery;
use App\WebshopBundle\Application\Checkout\PaymentMethod\CreatePaymentMethodQuery;
use App\WebshopBundle\Application\Checkout\ShippingMethod\CreateShippingMethodQuery;
use App\WebshopBundle\Application\Payment\GetPayment\GetPaymentQuery;
use App\WebshopBundle\Application\Shipping\GetShipping\GetShippingQuery;
use App\WebshopBundle\Domain\Model\Checkout\Dto\Address;
use App\WebshopBundle\Domain\Model\Checkout\Dto\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class CheckoutController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function index(Request $request)
    {
        if ($request->cookies->has('cart_id')) {
            /** @var CreateCheckoutOutput $checkoutOutput */
            $checkoutOutput = $this->handle(new CreateCheckoutQuery($request->cookies->get('cart_id')));
            $response = $this->render('@webshop/customer_space/checkout.html.twig', []);
            $response->headers->setCookie(Cookie::create('checkoutId', $checkoutOutput->getCheckoutId()));
            return $response;
        }

        return $this->redirect('/cart');
    }

    public function addCustomer(Request $request)
    {
        if (!$request->cookies->has('checkoutId')) {
            $this->redirect('/checkout');
        }
        $customer = new Customer(
            $request->get("email",null),
            $request->get("firstName",null),
            $request->get("lastName",null),
            $request->get("phone",null)
        );
        $this->handle(new CreateCustomerCommand(
            $request->cookies->get('checkoutId'),
            $customer
        ));

        $this->handle(new AddShippingAddressCommand(
            $request->cookies->get('checkoutId'),
            new Address(
                $request->get("address",null),
                $request->get("city",null),
                $request->get("postcode",null),
                $request->get("country",null),
            )
        ));

        $this->handle(new AddBillingAddressCommand(
            $request->cookies->get('checkoutId'),
            new Address(
                $request->get("address",null),
                $request->get("city",null),
                $request->get("postcode",null),
                $request->get("country",null),
            )
        ));
        return $this->redirect('/checkout/step/2');
    }

    public function createShippingMethod(Request $request)
    {
        if (!$request->cookies->has('checkoutId')) {
            return $this->redirect('/checkout');
        }

        $checkout = $this->handle(new GetCheckoutQuery($request->cookies->get('checkoutId')));

        $shippingMethods = $this->handle(new GetShippingQuery($checkout->getCheckout()->getCheckoutTotal()->getValue()));
        return $this->render('@webshop/customer_space/checkoutStep2.html.twig', ['shippingMethods'=> $shippingMethods->getShippingMethods()]);
    }

    public function addShippingMethod(Request $request)
    {
        if (!$request->cookies->has('checkoutId')) {
            return $this->redirect('/checkout');
        }

        $this->handle(new CreateShippingMethodQuery(
            $request->cookies->get('checkoutId'),
            $request->get('shippingMethodId')
        ));

        return $this->redirect('/checkout/step/3');
    }

    public function createPaymentMethod(Request $request)
    {
        if (!$request->cookies->has('checkoutId')) {
            return $this->redirect('/checkout');
        }

        $checkout = $this->handle(new GetCheckoutQuery($request->cookies->get('checkoutId')));

        $paymentMethods = $this->handle(new GetPaymentQuery($checkout->getCheckout()->getShippingMethod()->getShippingMethodId()));

        return $this->render('@webshop/customer_space/checkoutStep3.html.twig', ['paymentMethods'=> $paymentMethods->getPaymentMethods()]);
    }

    public function addPaymentMethod(Request $request)
    {
        if (!$request->cookies->has('checkoutId')) {
            return $this->redirect('/checkout');
        }

        $this->handle(new CreatePaymentMethodQuery(
            $request->cookies->get('checkoutId'),
            $request->get('paymentMethodId')
        ));

        return $this->redirect('/checkout/' . $request->cookies->get('checkoutId') . '/confirm');
    }

    public function confirm(Request $request)
    {
        if (!$request->cookies->has('checkoutId')) {
            return $this->redirect('/checkout');
        }

        try {
            $response = $this->handle(new ConfirmCheckoutCommand(
                $request->cookies->get('checkoutId')
            ));

            if (!empty($response['redirectUrl'])) {
                return $this->redirect($response['redirectUrl']);
            }
        } catch (HandlerFailedException $e) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->redirect('/thankyou');
    }

    public function completePayment(Request $request, string $checkoutId)
    {
        try {
            $this->handle(new ConfirmPaymentCommand(
                $checkoutId
            ));

        } catch (HandlerFailedException $e) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->redirect('/thankyou');
    }
}
