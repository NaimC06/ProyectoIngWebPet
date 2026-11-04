<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_ADMIN')]
final class AdminUsuarioController extends AbstractController
{
    #[Route(
        '/admin/usuarios',
        name: 'app_admin_usuarios',
        methods: ['GET']
    )]
    public function index(UsuarioRepository $repository): Response
    {
        $usuarios = $repository->findBy([], ['id' => 'ASC']);

        $resumen = [
            'total' => count($usuarios),
            'clientes' => 0,
            'paseadores' => 0,
            'administradores' => 0,
        ];

        foreach ($usuarios as $usuario) {
            if (in_array('ROLE_ADMIN', $usuario->getRoles(), true)) {
                $resumen['administradores']++;
            }

            if (in_array('ROLE_CLIENTE', $usuario->getRoles(), true)) {
                $resumen['clientes']++;
            }

            if (in_array('ROLE_PASEADOR', $usuario->getRoles(), true)) {
                $resumen['paseadores']++;
            }
        }

        return $this->render('admin/usuarios.html.twig', [
            'usuarios' => $usuarios,
            'resumen' => $resumen,
        ]);
    }

    #[Route(
        '/admin/usuarios/{id}/editar',
        name: 'app_admin_usuario_editar',
        methods: ['GET', 'POST']
    )]
    public function editar(
        Usuario $usuario,
        Request $request,
        UsuarioRepository $repository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $form = $this->createForm(UsuarioType::class, $usuario, [
            'es_nuevo' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->validarDuplicados($form, $usuario, $repository);

            $rol = $form->get('rol')->getData();

            if (
                $this->getUser() === $usuario
                && $rol !== 'ROLE_ADMIN'
            ) {
                $form->get('rol')->addError(
                    new FormError(
                        'No puedes quitarte tu propio rol de administrador.'
                    )
                );
            }

            if ($form->isValid()) {
                $usuario->setRoles([$rol]);

                $clave = $form->get('contrasenaNueva')->getData();

                if (is_string($clave) && $clave !== '') {
                    $usuario->setPassword(
                        $passwordHasher->hashPassword(
                            $usuario,
                            $clave
                        )
                    );
                }

                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Usuario actualizado correctamente.'
                );

                return $this->redirectToRoute('app_admin_usuarios');
            }
        }

        return $this->render('admin/usuario_form.html.twig', [
            'formulario' => $form,
            'es_nuevo' => false,
            'usuario' => $usuario,
        ]);
    }

    private function validarDuplicados(
        FormInterface $form,
        Usuario $usuario,
        UsuarioRepository $repository
    ): void {
        $correo = $repository->findOneBy([
            'email' => $usuario->getEmail(),
        ]);

        if ($correo !== null && $correo->getId() !== $usuario->getId()) {
            $form->get('email')->addError(
                new FormError('Este correo ya está registrado.')
            );
        }

        if ($usuario->getCedula() !== null) {
            $cedula = $repository->findOneBy([
                'cedula' => $usuario->getCedula(),
            ]);

            if (
                $cedula !== null
                && $cedula->getId() !== $usuario->getId()
            ) {
                $form->get('cedula')->addError(
                    new FormError('Esta cédula ya está registrada.')
                );
            }
        }
    }
}