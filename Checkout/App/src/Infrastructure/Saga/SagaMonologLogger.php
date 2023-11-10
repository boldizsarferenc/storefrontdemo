<?php

namespace App\Infrastructure\Saga;

use App\Domain\Saga\SagaLoggerInterface;
use Psr\Log\LoggerInterface;

class SagaMonologLogger implements SagaLoggerInterface
{
    public function __construct(private readonly LoggerInterface $sagaLogger)
    {
    }

    public function debug(string $message, array $context = []): void
    {
        $this->sagaLogger->debug($message, $context);
    }
}
