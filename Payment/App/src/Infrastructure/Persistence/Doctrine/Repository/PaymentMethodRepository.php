<?php

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\PaymentMethod;
use App\Domain\PaymentMethodId;
use App\Domain\PaymentMethodRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PaymentMethodRepository extends ServiceEntityRepository implements PaymentMethodRepositoryInterface
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentMethod::class);
    }

    public function getPaymentMethod(string $id): PaymentMethod
    {
        return $this->find($id);
    }
}
