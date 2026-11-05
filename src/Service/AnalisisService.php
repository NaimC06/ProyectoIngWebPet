<?php

namespace App\Service;

use App\Repository\UsuarioRepository;
use App\Repository\PlanRepository;
use App\Repository\SuscripcionRepository;
use App\Repository\PaseoRepository;
use App\Repository\PerroRepository;

final class AnalisisService
{
    public function __construct(
        private readonly UsuarioRepository $usuarios,
        private readonly PlanRepository $planes,
        private readonly SuscripcionRepository $suscripciones,
        private readonly PaseoRepository $paseos,
        private readonly PerroRepository $perros
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

    public function obtenerEstadisticasDashboard(): array
    {
        $resumen = $this->obtenerResumenInicial();
        
        // Financiero
        $todasSuscripciones = $this->suscripciones->findAll();
        $valorTotalSuscripciones = 0;
        $planesContador = [];
        $clientesConPlan = 0;

        foreach ($todasSuscripciones as $sus) {
            $valorTotalSuscripciones += (float) $sus->getPlan()->getPrecio();
            $nombrePlan = $sus->getPlan()->getNombre();
            $planesContador[$nombrePlan] = ($planesContador[$nombrePlan] ?? 0) + 1;
            if ($sus->getEstado() === 'activa') {
                $clientesConPlan++;
            }
        }

        $planMasUtilizado = !empty($planesContador) 
            ? array_search(max($planesContador), $planesContador) 
            : 'N/A';

        // Operativo
        $todosPaseos = $this->paseos->findAll();
        $totalPaseos = count($todosPaseos);
        $completados = 0;
        $programados = 0;
        $cancelados = 0;
        $cargaPaseadores = [];

        foreach ($todosPaseos as $paseo) {
            switch ($paseo->getEstado()) {
                case 'completado': $completados++; break;
                case 'programado': $programados++; break;
                case 'cancelado': $cancelados++; break;
            }
            if ($paseo->getPaseador()) {
                $p = $paseo->getPaseador()->getNombreCompleto();
                $cargaPaseadores[$p] = ($cargaPaseadores[$p] ?? 0) + 1;
            }
        }

        return [
            'usuarios' => $resumen,
            'financiero' => [
                'valorTotal' => $valorTotalSuscripciones,
                'clientesSuscritos' => $clientesConPlan,
                'planMasUtilizado' => $planMasUtilizado,
                'porcentajeSuscritos' => $resumen['clientes'] > 0 
                    ? round($clientesConPlan * 100 / $resumen['clientes'], 1) 
                    : 0,
            ],
            'operativo' => [
                'totalPaseos' => $totalPaseos,
                'completados' => $completados,
                'programados' => $programados,
                'cancelados' => $cancelados,
                'cargaPaseadores' => $cargaPaseadores,
            ],
            'general' => [
                'totalPerros' => count($this->perros->findAll()),
                'suscripcionesActivas' => $clientesConPlan,
            ]
        ];
    }

    public function obtenerReporteCompleto(?string $mes, ?string $anio): array
    {
        $todosPaseos = $this->paseos->findAll();
        $filtrados = $todosPaseos;

        if ($mes && $anio) {
            $filtrados = array_filter($todosPaseos, function($p) use ($mes, $anio) {
                return $p->getFechaHora()->format('m') === $mes 
                    && $p->getFechaHora()->format('Y') === $anio;
            });
        }

        $stats = ['completado' => 0, 'programado' => 0, 'cancelado' => 0];
        foreach ($filtrados as $p) {
            $stats[$p->getEstado()] = ($stats[$p->getEstado()] ?? 0) + 1;
        }

        return [
            'periodo' => ['mes' => $mes, 'anio' => $anio],
            'total' => count($filtrados),
            'porEstados' => $stats,
            'usuariosConPlan' => count($this->suscripciones->findBy(['estado' => 'activa'])),
            'usuariosSinPlan' => $this->obtenerResumenInicial()['clientes'] - count($this->suscripciones->findBy(['estado' => 'activa'])),
        ];
    }

    public function obtenerAnalisisRiesgos(): array
    {
        $riesgos = [];
        $ahora = new \DateTimeImmutable();

        // 1. Suscripciones próximas a vencer (7 días)
        $vencimientos = $this->suscripciones->findAll();
        $proximosVencer = array_filter($vencimientos, function($s) use ($ahora) {
            $diff = $ahora->diff($s->getFechaFin());
            return $s->getEstado() === 'activa' && $diff->days <= 7 && $diff->invert === 0;
        });
        $riesgos['vencimientos'] = [
            'valor' => count($proximosVencer),
            'nivel' => count($proximosVencer) > 3 ? 'alto' : 'medio',
            'descripcion' => 'Suscripciones que vencen en menos de 7 días'
        ];

        // 2. Clientes sin plan
        $totalClientes = $this->obtenerResumenInicial()['clientes'];
        $conPlan = count($this->suscripciones->findBy(['estado' => 'activa']));
        $riesgos['sinPlan'] = [
            'valor' => $totalClientes - $conPlan,
            'nivel' => ($totalClientes - $conPlan) > 2 ? 'medio' : 'bajo',
            'descripcion' => 'Clientes registrados sin suscripción activa'
        ];

        return $riesgos;
    }
}
