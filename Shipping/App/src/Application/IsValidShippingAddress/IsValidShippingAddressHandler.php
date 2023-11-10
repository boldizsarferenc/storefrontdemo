<?php

namespace App\Application\IsValidShippingAddress;

class IsValidShippingAddressHandler
{
    public function __invoke(IsValidShippingAddressQuery $query): bool
    {
        return $query->getCity() === 'Debrecen';
    }
}
