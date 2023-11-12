<?php

namespace App\Infrastructure\Adapter\ShippingApi;

use App\Domain\Api\ShippingApiInterface;
use App\Domain\Checkout;
use App\Domain\Shared\EntityIdGeneratorInterface;
use App\Domain\ShippingMethod;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ShippingApi implements ShippingApiInterface
{

    public function __construct(
        private HttpClientInterface $client,
        private EntityIdGeneratorInterface $entityIdGenerator
    ) {
        $this->client = $client->withOptions([
            'base_uri' => 'http://api_gateway_nginx:8080/shipping/'
        ]);
    }

    public function getShippingMethod(string $externalShippingMethodId): ShippingMethod
    {
        $response = $this->client->request('GET', "shipping-methods/$externalShippingMethodId");
        $data = json_decode($response->getContent(), true);
        //TODO $data['shippingLanes'][0]['cost'] helyett kiválasztani amegfelelő shippingLane-t a min és max kosárérték alapján
        return new ShippingMethod(
            $this->entityIdGenerator->generate(),
            $externalShippingMethodId,
            $data['name'],
            $data['shippingLanes'][0]['cost']
        );
    }

    public function isValidAddress(Checkout $checkout): bool
    {
        $address = $checkout->getShippingAddress();
        $response = $this->client->request('POST', "is-valid-address", [
            'json' => [
                'address' => $address->getAddress(),
                'country' => $address->getCountry(),
                'postcode' => $address->getPostcode(),
                'city' => $address->getCity()
            ]
        ]);
        $data = json_decode($response->getContent(), true);

        return $data['isValid'];
    }
}
