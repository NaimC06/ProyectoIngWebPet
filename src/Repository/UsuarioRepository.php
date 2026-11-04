<?php

namespace App\Repository;

use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repositorio de usuarios de Pet Concierge.
 */
class UsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    public function buscarPorCorreo(string $correo): ?Usuario
    {
        return $this->findOneBy([
            'email' => mb_strtolower(trim($correo)),
        ]);
    }

    /**
     * Devuelve todos los usuarios con rol ROLE_CLIENTE.
     *
     * @return Usuario[]
     */
    public function findClientes(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :rol')
            ->setParameter('rol', '%ROLE_CLIENTE%')
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
