<?php

namespace App\Controller;

use App\Entity\Suscripcion;
use App\Form\SuscripcionType;
use App\Repository\SuscripcionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class AdminSuscripcionController extends AbstractController
{
    #[Route('/admin/suscripciones', name: 'app_admin_suscripciones', methods: ['GET'])]
    public function index(SuscripcionRepository $repository): Response
    {
        $suscripciones = $repository->findBy([], ['fechaInicio' => 'DESC']);

        return $this->render('admin/suscripciones.html.twig', [
            'suscripciones' => $suscripciones,
        ]);
    }

    #[Route('/admin/suscripciones/nueva', name: 'app_admin_suscripcion_nueva', methods: ['GET', 'POST'])]
    public function nueva(Request $request, EntityManagerInterface $entityManager): Response
    {
        $suscripcion = new Suscripcion();
        $form = $this->createForm(SuscripcionType::class, $suscripcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($suscripcion);
            $entityManager->flush();

            $this->addFlash('success', 'Suscripción creada correctamente.');

            return $this->redirectToRoute('app_admin_suscripciones');
        }

        return $this->render('admin/suscripcion_form.html.twig', [
            'formulario' => $form,
            'es_nuevo' => true,
        ]);
    }

    #[Route('/admin/suscripciones/{id}', name: 'app_admin_suscripcion_ver', methods: ['GET'])]
    public function ver(Suscripcion $suscripcion): Response
    {
        return $this->render('admin/suscripcion_ver.html.twig', [
            'suscripcion' => $suscripcion,
        ]);
    }

    #[Route('/admin/suscripciones/{id}/editar', name: 'app_admin_suscripcion_editar', methods: ['GET', 'POST'])]
    public function editar(Request $request, Suscripcion $suscripcion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SuscripcionType::class, $suscripcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Suscripción actualizada correctamente.');

            return $this->redirectToRoute('app_admin_suscripciones');
        }

        return $this->render('admin/suscripcion_form.html.twig', [
            'formulario' => $form,
            'es_nuevo' => false,
            'suscripcion' => $suscripcion,
        ]);
    }

    #[Route('/admin/suscripciones/{id}/eliminar', name: 'app_admin_suscripcion_eliminar', methods: ['POST'])]
    public function eliminar(Request $request, Suscripcion $suscripcion, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_csrf_token');

        if (
            !is_string($token)
            || !$this->isCsrfTokenValid('delete_suscripcion_' . $suscripcion->getId(), $token)
        ) {
            throw $this->createAccessDeniedException('Token CSRF inválido.');
        }

        $entityManager->remove($suscripcion);
        $entityManager->flush();

        $this->addFlash('success', 'Suscripción eliminada correctamente.');

        return $this->redirectToRoute('app_admin_suscripciones');
    }
}
