<?php

namespace App\Domain\Saga;

interface WorkflowInterface
{
    public function can(object $subject, string $transitionName): bool;

    public function apply(object $subject, string $transitionName, array $context = []): void;
}
