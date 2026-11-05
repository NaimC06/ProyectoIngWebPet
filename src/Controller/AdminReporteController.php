<?php

namespace App\Controller;

use App\Repository\PaseoRepository;
use App\Repository\PlanRepository;
use App\Repository\SuscripcionRepository;
use App\Repository\UsuarioRepository;
use App\Repository\PerroRepository;
use App\Service\AnalisisService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class AdminReporteController extends AbstractController
{
    public function __construct(
        private readonly AnalisisService $analisis,
        private readonly UsuarioRepository $usuarios,
        private readonly PlanRepository $planes,
        private readonly SuscripcionRepository $suscripciones,
        private readonly PaseoRepository $paseos,
        private readonly PerroRepository $perros
    ) {
    }

    #[Route('/admin/reportes', name: 'app_admin_reportes', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $mes = $request->query->getInt('mes', (int) date('m'));
        $anio = $request->query->getInt('anio', (int) date('Y'));

        $reporte = $this->analisis->obtenerReporteCompleto($mes, $anio);

        return $this->render('admin/reportes.html.twig', [
            'reporte' => $reporte,
            'mes_actual' => $mes,
            'anio_actual' => $anio,
        ]);
    }
}
