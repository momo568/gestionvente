<?php

namespace App\Repository;

use App\Entity\Promotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PromotionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promotion::class);
    }

    // 🔎 Trouver une promo active par code
    public function findActiveByCode(string $code): ?Promotion
    {
        $now = new \DateTimeImmutable();
        return $this->createQueryBuilder('p')
            ->andWhere('p.code = :code')
            ->andWhere('p.dateDebut <= :now')
            ->andWhere('p.dateFin >= :now')
            ->setParameters(['code' => $code, 'now' => $now])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
