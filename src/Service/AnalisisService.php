<?php

namespace App\Service;

use App\Repository\UsuarioRepository;

final class AnalisisService
{
    public function __construct(
        private readonly UsuarioRepository $usuarios
    ) {
    }

    public function obtenerResumenInicial(): array
    {
        $usuarios = $this->usuarios->findBy(
            [],
            ['id' => 'DESC']
        );

        $total = count($usuarios);
        $clientes = 0;
        $paseadores = 0;
        $administradores = 0;
        $activos = 0;

        foreach ($usuarios as $usuario) {
            $roles = $usuario->getRoles();

            if (in_array('ROLE_ADMIN', $roles, true)) {
                $administradores++;
            } elseif (in_array('ROLE_PASEADOR', $roles, true)) {
                $paseadores++;
            } elseif (in_array('ROLE_CLIENTE', $roles, true)) {
                $clientes++;
            }

            if ($usuario->isActivo()) {
                $activos++;
            }
        }

        return [
            'total' => $total,
            'clientes' => $clientes,
            'paseadores' => $paseadores,
            'administradores' => $administradores,
            'activos' => $activos,

            'porcentajeClientes' => $total > 0
                ? round($clientes * 100 / $total, 1)
                : 0,

            'porcentajePaseadores' => $total > 0
                ? round($paseadores * 100 / $total, 1)
                : 0,

            'porcentajeAdministradores' => $total > 0
                ? round($administradores * 100 / $total, 1)
                : 0,

            'ultimosUsuarios' => array_slice(
                $usuarios,
                0,
                5
            ),
        ];
    }
}