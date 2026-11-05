<?php

namespace App\Controller;

use App\Service\AnalisisService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
final class AdminController extends AbstractController
{
    #[Route(
        "/admin",
        name: "app_admin_dashboard",
        methods: ["GET"]
    )]
    public function index(
        AnalisisService $analisis
    ): Response {
        $carpeta = dirname(__DIR__, 2)
            . "/public/resources/images/";

        $imagen = null;

        foreach (
            ["png", "webp", "jpg", "jpeg"] as $extension
        ) {
            $archivo = "BlueyEstadisticas." . $extension;

            if (is_file($carpeta . $archivo)) {
                $imagen = "resources/images/" . $archivo;
                break;
            }
        }

        return $this->render(
            "admin/estadisticas.html.twig",
            [
                "dashboard" => $analisis->obtenerEstadisticasDashboard(),
                "imagen_estadisticas" => $imagen,
            ]
        );
    }

    #[Route(
        "/admin/estadisticas",
        name: "app_admin_estadisticas",
        methods: ["GET"]
    )]
    public function estadisticas(): Response
    {
        return $this->redirectToRoute(
            "app_admin_dashboard"
        );
    }
}
