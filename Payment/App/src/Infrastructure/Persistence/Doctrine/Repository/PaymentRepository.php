<?php

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Payment;
use App\Domain\PaymentId;
use \App\Domain\PaymentRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PaymentRepository extends ServiceEntityRepository implements PaymentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function get(PaymentId $id): Payment
    {
        return $this->find($id->getId());
    }

    public function add(Payment $payment) {
        $this->getEntityManager()->persist($payment);
        $this->getEntityManager()->flush();
    }

    public function save(): void {
        $this->getEntityManager()->flush();
    }

    public function getByCheckoutId(string $checkoutId): Payment
    {
        return $this->findOneBy(['checkoutId' => $checkoutId]);
    }
}
