<?php

namespace App\Infrastructure\Adapter\PaymentApi;


use App\Domain\Api\PaymentApiInterface;
use App\Domain\Customer;
use App\Domain\PaymentMethod;
use App\Domain\PaymentStatus;
use App\Domain\Shared\EntityIdGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymentApi implements PaymentApiInterface
{
    public function __construct(
        private HttpClientInterface $client,
        private EntityIdGeneratorInterface $entityIdGenerator
    ) {
        $this->client = $client->withOptions([
            'base_uri' => 'http://api_gateway_nginx:8080'
        ]);
    }

    public function getPaymentMethod(string $externalPaymentMethodId): PaymentMethod
    {
        $response = $this->client->request('GET', "/payment/api/paymentMethod/list/$externalPaymentMethodId");
        $data = json_decode($response->getContent(), true);
        //TODO $data['paymentMethods'][0]['cost'] helyett kiválasztani a megfelelő paymentMethod-ot
        return new PaymentMethod(
            $this->entityIdGenerator->generate(),
            $externalPaymentMethodId,
            $data['paymentMethods'][0]['name'],
            $data['paymentMethods'][0]['fee']
        );
    }

    public function createPaymentMethod(string $checkoutId, string $externalPaymentMethodId, Customer $customer, float $cartTotal): PaymentStatus
    {
        $response = $this->client->request('POST', '/payment/api/payment/initiate', [
                'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'checkoutId' => $checkoutId,
                'paymentMethodId' => $externalPaymentMethodId,
                'customer' => [
                    'customerId' => $customer->getCustomerId()->getValue(),
                    'email' => $customer->getEmail(),
                    'firstName' => $customer->getFirstName(),
                    'lastName' => $customer->getLastName(),
                    'phone' => $customer->getPhone()
                ],
                'amount' => $cartTotal
            ]
        ]);
        $data = json_decode($response->getContent(), true);

        return new PaymentStatus($data['status'] ?? 'error', $data['redirectUrl'] ?? null);
    }

    public function getPaymentStatus(string $externalPaymentMethodId): PaymentStatus
    {
        return new PaymentStatus('SUCCESS', '');
    }
}
