<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        // El administrador tiene su propia página principal.
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute(
                'app_admin_dashboard'
            );
        }

        $carpeta = dirname(__DIR__, 2)
            . '/public/resources/images/';

        $nombres = [
            'paseo' => [
                'BluePaseo',
                'BlueyPaseo',
            ],
            'sentado' => [
                'BlueySentado',
            ],
            'amigos' => [
                'BlueysYAmigos',
                'BlueysYAmigos',
            ],
            'estadisticas' => [
                'BlueyEstadisticas',
            ],
        ];

        $imagenes = [];

        foreach ($nombres as $clave => $alternativas) {
            $imagenes[$clave] = null;

            foreach ($alternativas as $nombre) {
                foreach (
                    ['.png', '.webp', '.jpg', '.jpeg'] as $extension
                ) {
                    $archivo = $nombre . $extension;

                    if (is_file($carpeta . $archivo)) {
                        $imagenes[$clave] =
                            'resources/images/' . $archivo;

                        break 2;
                    }
                }
            }
        }

        return $this->render(
            'home/index.html.twig',
            ['imagenes' => $imagenes]
        );
    }
}