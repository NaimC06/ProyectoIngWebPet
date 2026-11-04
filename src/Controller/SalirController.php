<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SalirController extends AbstractController
{
    #[Route(
        '/salir',
        name: 'app_salir',
        methods: ['POST']
    )]
    public function salir(
        Request $request,
        Security $security
    ): Response {
        $token = $request->request->get('_csrf_token');

        if (
            !is_string($token)
            || !$this->isCsrfTokenValid('logout', $token)
        ) {
            throw $this->createAccessDeniedException(
                'Token de cierre de sesión inválido.'
            );
        }

        // Symfony elimina la autenticación actual.
        // El token CSRF ya fue validado arriba.
        $respuesta = $security->logout(false);

        // Regresar a la portada pública.
        return $respuesta
            ?? $this->redirectToRoute('app_home');
    }
}