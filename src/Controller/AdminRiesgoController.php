<?php

namespace App\Controller;

use App\Repository\SuscripcionRepository;
use App\Repository\PaseoRepository;
use App\Repository\PlanRepository;
use App\Repository\UsuarioRepository;
use App\Service\AnalisisService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class AdminRiesgoController extends AbstractController
{
    public function __construct(
        private readonly AnalisisService $analisis,
        private readonly UsuarioRepository $usuarios,
        private readonly PlanRepository $planes,
        private readonly SuscripcionRepository $suscripciones,
        private readonly PaseoRepository $paseos
    ) {
    }

    #[Route('/admin/riesgos', name: 'app_admin_riesgos', methods: ['GET'])]
    public function index(): Response
    {
        $riesgos = $this->analisis->obtenerAnalisisRiesgos();

        return $this->render('admin/riesgos.html.twig', [
            'riesgos' => $riesgos,
        ]);
    }
}
