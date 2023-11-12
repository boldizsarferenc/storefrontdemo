<?php

namespace App\Infrastructure\Saga;

use App\Domain\Saga\WorkflowInterface;
use Symfony\Component\Workflow\WorkflowInterface as SymfonyWorkflowInterface;

class Workflow implements WorkflowInterface
{
    public function __construct(private readonly SymfonyWorkflowInterface $workflow)
    {
    }

    public function can(object $subject, string $transitionName): bool
    {
        return $this->workflow->can($subject, $transitionName);
    }

    public function apply(object $subject, string $transitionName, array $context = []): void
    {
        $this->workflow->apply($subject, $transitionName, $context);
    }
}
