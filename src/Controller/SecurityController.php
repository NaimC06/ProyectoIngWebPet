<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $imagen = null;

        $carpeta = dirname(__DIR__, 2)
            . '/public/resources/images/';

        foreach (['png', 'webp', 'jpg', 'jpeg'] as $extension) {
            $archivo = 'BlueyFrisbySalto.' . $extension;

            if (is_file($carpeta . $archivo)) {
                $imagen = 'resources/images/' . $archivo;
                break;
            }
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'imagen_login' => $imagen,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        throw new \LogicException(
            'Symfony Security debe interceptar esta ruta de logout.'
        );
    }
}