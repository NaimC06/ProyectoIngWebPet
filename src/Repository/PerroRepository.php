<?php

namespace App\Repository;

use App\Entity\Perro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PerroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Perro::class);
    }

    public function findByPropietario(int $propietarioId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.propietario = :propietario')
            ->setParameter('propietario', $propietarioId)
            ->orderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
