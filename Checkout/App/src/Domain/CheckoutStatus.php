<?php

namespace App\Domain;

enum CheckoutStatus: string
{
    case STATUS_PENDING = 'pending';
    case STATUS_STOCK_RESERVED = 'stock_reserved';
    case STATUS_PAYMENT_IN_PROGRESS = 'payment_in_progress';
    case STATUS_PAID = 'paid';
    case STATUS_STATUS_CHECKED = 'status_checked';
    case STATUS_SHIPPING_CHECKED = 'shipping_checked';
    case STATUS_COMPLETED = 'completed';
    case STATUS_FAILED = 'failed';

    public static function fromValue(string $name)
    {
        $name = mb_strtoupper($name);
        return constant("self::STATUS_$name");
    }
}
