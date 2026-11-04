<?php

namespace App\Repository;

use App\Entity\Plan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plan::class);
    }

    public function findActivos(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.estado = :estado')
            ->setParameter('estado', 'activo')
            ->orderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
