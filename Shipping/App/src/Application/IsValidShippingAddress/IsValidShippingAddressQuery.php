<?php

namespace App\Application\IsValidShippingAddress;

class IsValidShippingAddressQuery
{
    public function __construct(
        private readonly string $address,
        private readonly string $country,
        private readonly string $postcode,
        private readonly string $city
    ) {}

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

}
