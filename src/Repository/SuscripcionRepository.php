<?php

namespace App\Repository;

use App\Entity\Suscripcion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SuscripcionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suscripcion::class);
    }

    public function findActivas(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.estado = :estado')
            ->setParameter('estado', 'activa')
            ->orderBy('s.fechaInicio', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByUsuario(int $usuarioId): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.usuario = :usuario')
            ->setParameter('usuario', $usuarioId)
            ->orderBy('s.fechaInicio', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
