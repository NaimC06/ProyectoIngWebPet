<?php

namespace App\Controller;

use App\Entity\Perro;
use App\Entity\Usuario;
use App\Form\RegistroType;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegistroController extends AbstractController
{
    #[Route(
        '/registro',
        name: 'app_registro',
        methods: ['GET', 'POST']
    )]
    public function registrar(
        Request $request,
        UsuarioRepository $usuarios,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($this->getUser() !== null) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(RegistroType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $tipo = $form->get('tipoCuenta')->getData();
            $email = mb_strtolower(trim(
                (string) $form->get('email')->getData()
            ));
            $cedula = trim(
                (string) $form->get('cedula')->getData()
            );

            if ($usuarios->findOneBy(['email' => $email])) {
                $form->get('email')->addError(
                    new FormError('Este correo ya está registrado.')
                );
            }

            if (
                $cedula !== ''
                && $usuarios->findOneBy(['cedula' => $cedula])
            ) {
                $form->get('cedula')->addError(
                    new FormError('Esta cédula ya está registrada.')
                );
            }

            if ($tipo === 'ROLE_CLIENTE') {
                foreach ([
                    'perroNombre' => 'Ingresa el nombre de tu perro.',
                    'perroRaza' => 'Ingresa la raza de tu perro.',
                    'perroTamano' => 'Selecciona el tamaño de tu perro.',
                ] as $campo => $mensaje) {
                    $valor = $form->get($campo)->getData();

                    if ($valor === null || trim((string) $valor) === '') {
                        $form->get($campo)->addError(
                            new FormError($mensaje)
                        );
                    }
                }

                if ($form->get('perroEdad')->getData() === null) {
                    $form->get('perroEdad')->addError(
                        new FormError('Ingresa la edad de tu perro.')
                    );
                }
            }

            if ($tipo === 'ROLE_PASEADOR') {
                if (!$form->get('zonaTrabajo')->getData()) {
                    $form->get('zonaTrabajo')->addError(
                        new FormError('Selecciona una zona de trabajo.')
                    );
                }
            }

            if (
                $form->isValid()
                && in_array(
                    $tipo,
                    ['ROLE_CLIENTE', 'ROLE_PASEADOR'],
                    true
                )
            ) {
                $datos = $form->getData();

                $usuario = new Usuario();
                $usuario->setNombre($datos['nombre']);
                $usuario->setApellido($datos['apellido']);
                $usuario->setEmail($email);
                $usuario->setCedula($cedula);
                $usuario->setTelefono($datos['telefono']);
                $usuario->setRoles([$tipo]);
                $usuario->setActivo(true);

                if ($tipo === 'ROLE_PASEADOR') {
                    $usuario->setZonaTrabajo(
                        $datos['zonaTrabajo']
                    );
                }

                $usuario->setPassword(
                    $passwordHasher->hashPassword(
                        $usuario,
                        $datos['clave']
                    )
                );

                $entityManager->persist($usuario);

                if ($tipo === 'ROLE_CLIENTE') {
                    $perro = new Perro();
                    $perro->setPropietario($usuario);
                    $perro->setNombre($datos['perroNombre']);
                    $perro->setRaza($datos['perroRaza']);
                    $perro->setEdad($datos['perroEdad']);
                    $perro->setTamano($datos['perroTamano']);

                    $entityManager->persist($perro);
                }

                // Doctrine guarda el usuario y el perro
                // en la misma operación de persistencia.
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Tu cuenta fue creada correctamente. Ya puedes iniciar sesión.'
                );

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/registro.html.twig', [
            'formulario' => $form,
        ]);
    }
}