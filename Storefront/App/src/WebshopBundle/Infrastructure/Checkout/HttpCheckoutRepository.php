<?php

namespace App\WebshopBundle\Infrastructure\Checkout;

use App\WebshopBundle\Domain\Exception\DomainException;
use App\WebshopBundle\Domain\Model\Checkout\Checkout;
use App\WebshopBundle\Domain\Model\Checkout\CheckoutRepositoryInterface;
use App\WebshopBundle\Domain\Model\Checkout\Dto\Address;
use App\WebshopBundle\Domain\Model\Checkout\Dto\CheckoutTotal;
use App\WebshopBundle\Domain\Model\Checkout\Dto\Customer;
use App\WebshopBundle\Domain\Model\Checkout\Dto\PaymentMethod;
use App\WebshopBundle\Domain\Model\Checkout\Dto\ShippingMethod;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use function json_decode;

class HttpCheckoutRepository implements CheckoutRepositoryInterface
{
    public function __construct(
        private string $baseUrl,
        private Client $client = new Client(),
        private string $resourceUri = ''
    ) {
    }

    /**
     * @throws DomainException
     */
    private function sendPostRequest(array $data): array
    {
        try {
            $url = $this->baseUrl . '/' . $this->resourceUri;

            $response = $this->client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => $data,
                'verify' => 0
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new DomainException($e->getMessage());
        }
    }

    /**
     * @throws DomainException
     */
    private function sendGetRequest(): array
    {
        try {
            $url = $this->baseUrl . '/' . $this->resourceUri;
            $response = $this->client->get($url, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'verify' => 0
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new DomainException($e->getMessage());
        }
    }

    private function buildCheckoutDtoFromResponse(array $responseData): Checkout
    {
        $checkout = new Checkout();
        $checkout->setId($responseData['checkoutId']);
        $checkout->setStatus($responseData['status'] ?? '');
        if ($responseData['customer']) {
            $checkout->setCustomer(new Customer(
                $responseData['customer']['email'],
                $responseData['customer']['firstName'],
                $responseData['customer']['lastName'],
                $responseData['customer']['phone']
            ));
        }
        $checkoutTotal = new CheckoutTotal(
            (float)($responseData['cart']['total'] ?? 0),
            $responseData['checkoutTotal']['currency'] ?? 'HUF'
        );
        if ($responseData['checkoutTotal'] ?? null) {
            $checkoutTotal = new CheckoutTotal(
                (float)$responseData['checkoutTotal']['value'],
                $responseData['checkoutTotal']['currency']
            );
        }
        $checkout->setCheckoutTotal($checkoutTotal);
        if ($responseData['paymentMethod']) {
            $checkout->setPaymentMethod(new PaymentMethod(
                $responseData['paymentMethod']['paymentMethodId'],
                $responseData['paymentMethod']['paymentMethodName'],
                (float)$responseData['paymentMethod']['paymentFee']
            ));
        }
        if ($responseData['shippingMethod']) {
            $checkout->setShippingMethod(new ShippingMethod(
                $responseData['shippingMethod']['shippingMethodId'],
                $responseData['shippingMethod']['shippingMethodName'],
                (float)$responseData['shippingMethod']['shippingFee']
            ));
        }
        if ($responseData['billingAddress']) {
            $checkout->setBillingAddress(new Address(
                $responseData['billingAddress']['address'],
                $responseData['billingAddress']['city'],
                $responseData['billingAddress']['postcode'],
                $responseData['billingAddress']['country']
            ));
        }
        if ($responseData['shippingAddress']) {
            $checkout->setShippingAddress(new Address(
                $responseData['shippingAddress']['address'],
                $responseData['shippingAddress']['city'],
                $responseData['shippingAddress']['postcode'],
                $responseData['shippingAddress']['country']
            ));
        }
        return $checkout;
    }

    /**
     * @throws DomainException
     */
    public function createCheckout(string $cartId): Checkout
    {
        $this->resourceUri = 'checkout';
        $response = $this->sendPostRequest(['cartId' => $cartId]);

        return $this->buildCheckoutDtoFromResponse($response);
    }

    /**
     * @throws DomainException
     */
    public function addCustomer(string $checkoutId, Customer $customer): Checkout
    {
        $this->resourceUri = '/checkout/' . $checkoutId . '/customer';
        $data = [
            'email' => $customer->getEmail(),
            'lastName' => $customer->getLastName(),
            'firstName' => $customer->getFirstName(),
            'phone' => $customer->getPhone()
        ];
        $response = $this->sendPostRequest($data);

        return $this->buildCheckoutDtoFromResponse($response);
    }

    /**
     * @throws DomainException
     */
    public function addShippingAddress(string $checkoutId, Address $address): Checkout
    {
        $this->resourceUri = 'checkout/' . $checkoutId . '/shipping-address';
        $data = [
            'country' => $address->getCountry(),
            'postcode' => $address->getPostcode(),
            'city' => $address->getCity(),
            'address' => $address->getAddress()
        ];
        $response = $this->sendPostRequest($data);

        return $this->buildCheckoutDtoFromResponse($response);
    }

    /**
     * @throws DomainException
     */
    public function addBillingAddress(string $checkoutId, Address $address): Checkout
    {
        $this->resourceUri = 'checkout/' . $checkoutId . '/payment-address';
        $data = [
            'country' => $address->getCountry(),
            'postcode' => $address->getPostcode(),
            'city' => $address->getCity(),
            'address' => $address->getAddress()
        ];
        $response = $this->sendPostRequest($data);

        return $this->buildCheckoutDtoFromResponse($response);
    }

    /**
     * @throws DomainException
     */
    public function addShippingMethod(string $checkoutId, string $shippingMethodId): Checkout
    {
        $this->resourceUri = 'checkout/' . $checkoutId . '/shipping-method';
        $data = [
            'shippingMethodId' => $shippingMethodId
        ];
        $response = $this->sendPostRequest($data);

        return $this->buildCheckoutDtoFromResponse($response);
    }

    /**
     * @throws DomainException
     */
    public function addPaymentMethod(string $checkoutId, string $paymentMethodId): Checkout
    {
        $this->resourceUri = 'checkout/' . $checkoutId . '/payment-method';
        $data = [
            'paymentMethodId' => $paymentMethodId
        ];
        $response = $this->sendPostRequest($data);

        return $this->buildCheckoutDtoFromResponse($response);
    }

    /**
     * @throws DomainException
     */
    public function confirmCheckout(string $checkoutId): array
    {
        $this->resourceUri = 'checkout/' . $checkoutId . '/confirm';
        $response = $this->sendPostRequest([]);
        return $response;
    }

    /**
     * @throws DomainException
     */
    public function confirmPayment(string $checkoutId): Checkout
    {
        $this->resourceUri = 'checkout/' . $checkoutId . '/complete-payment';
        $response = $this->sendPostRequest([]);

        return $this->buildCheckoutDtoFromResponse($response) ;
    }

    /**
     * @throws DomainException
     */
    public function getCheckout(string $checkoutId): ?Checkout
    {
        $this->resourceUri = 'checkout/' . $checkoutId;

        return $this->buildCheckoutDtoFromResponse($this->sendGetRequest());
    }
}
