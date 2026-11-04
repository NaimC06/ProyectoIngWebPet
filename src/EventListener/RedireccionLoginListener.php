<?php

namespace App\EventListener;

use App\Entity\Usuario;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class)]
final class RedireccionLoginListener
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function __invoke(
        LoginSuccessEvent $event
    ): void {
        $usuario = $event->getUser();

        if (!$usuario instanceof Usuario) {
            return;
        }

        if (
            in_array(
                'ROLE_ADMIN',
                $usuario->getRoles(),
                true
            )
        ) {
            $url = $this->urlGenerator->generate(
                'app_admin_dashboard'
            );

            $event->setResponse(
                new RedirectResponse($url)
            );
        }
    }
}