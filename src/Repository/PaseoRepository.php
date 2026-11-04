<?php

namespace App\Repository;

use App\Entity\Paseo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PaseoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paseo::class);
    }

    public function findByCliente(int $clienteId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.cliente = :cliente')
            ->setParameter('cliente', $clienteId)
            ->orderBy('p.fechaHora', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByPaseador(int $paseadorId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.paseador = :paseador')
            ->setParameter('paseador', $paseadorId)
            ->orderBy('p.fechaHora', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findCompletados(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.estado = :estado')
            ->setParameter('estado', 'completado')
            ->orderBy('p.fechaHora', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getIngresosTotales(): string
    {
        $result = $this->createQueryBuilder('p')
            ->select('COALESCE(SUM(p.precio), 0)')
            ->where('p.estado = :estado')
            ->setParameter('estado', 'completado')
            ->getQuery()
            ->getSingleScalarResult();

        return (string) $result;
    }
}
