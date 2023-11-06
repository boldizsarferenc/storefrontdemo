<?php

namespace App\Domain;

enum CheckoutStatus: string
{
    case STATUS_PENDING = 'pending';
    case STATUS_UNPAID = 'unpaid';
    case STATUS_PAID = 'paid';
    case STATUS_COMPLETED = 'completed';
    case STATUS_FAILED = 'failed';

    public static function fromValue(string $name)
    {
        $name = mb_strtoupper($name);
        return constant("self::STATUS_$name");
    }
}
