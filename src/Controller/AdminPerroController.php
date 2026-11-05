<?php

namespace App\Controller;

use App\Entity\Perro;
use App\Form\PerroType;
use App\Repository\PerroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class AdminPerroController extends AbstractController
{
    #[Route('/admin/perros', name: 'app_admin_perros', methods: ['GET'])]
    public function index(PerroRepository $repository): Response
    {
        $perros = $repository->findBy([], ['id' => 'ASC']);

        return $this->render('admin/perros.html.twig', [
            'perros' => $perros,
        ]);
    }

    #[Route('/admin/perros/nuevo', name: 'app_admin_perro_nuevo', methods: ['GET', 'POST'])]
    public function nuevo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $perro = new Perro();
        $form = $this->createForm(PerroType::class, $perro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($perro);
            $entityManager->flush();

            $this->addFlash('success', 'Perro creado correctamente.');

            return $this->redirectToRoute('app_admin_perros');
        }

        return $this->render('admin/perro_form.html.twig', [
            'formulario' => $form,
            'es_nuevo' => true,
        ]);
    }

    #[Route('/admin/perros/{id}', name: 'app_admin_perro_ver', methods: ['GET'])]
    public function ver(Perro $perro): Response
    {
        return $this->render('admin/perro_ver.html.twig', [
            'perro' => $perro,
        ]);
    }

    #[Route('/admin/perros/{id}/editar', name: 'app_admin_perro_editar', methods: ['GET', 'POST'])]
    public function editar(Request $request, Perro $perro, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PerroType::class, $perro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Perro actualizado correctamente.');

            return $this->redirectToRoute('app_admin_perros');
        }

        return $this->render('admin/perro_form.html.twig', [
            'formulario' => $form,
            'es_nuevo' => false,
            'perro' => $perro,
        ]);
    }

    #[Route('/admin/perros/{id}/eliminar', name: 'app_admin_perro_eliminar', methods: ['POST'])]
    public function eliminar(Request $request, Perro $perro, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_csrf_token');

        if (
            !is_string($token)
            || !$this->isCsrfTokenValid('delete_perro_' . $perro->getId(), $token)
        ) {
            throw $this->createAccessDeniedException('Token CSRF inválido.');
        }

        $entityManager->remove($perro);
        $entityManager->flush();

        $this->addFlash('success', 'Perro eliminado correctamente.');

        return $this->redirectToRoute('app_admin_perros');
    }
}
