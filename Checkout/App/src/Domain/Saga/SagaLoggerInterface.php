<?php

namespace App\Domain\Saga;

interface SagaLoggerInterface
{
    public function debug(string $message, array $context = []): void;
}
