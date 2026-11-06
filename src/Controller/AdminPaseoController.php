<?php

namespace App\Controller;

use App\Entity\Paseo;
use App\Form\PaseoType;
use App\Repository\PaseoRepository;
use App\Repository\PerroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class AdminPaseoController extends AbstractController
{
    #[Route('/admin/paseos', name: 'app_admin_paseos', methods: ['GET'])]
    public function index(PaseoRepository $repository): Response
    {
        $paseos = $repository->findBy([], ['fechaHora' => 'DESC']);

        return $this->render('admin/paseos.html.twig', [
            'paseos' => $paseos,
        ]);
    }

    #[Route('/admin/paseos/nuevo', name: 'app_admin_paseo_nuevo', methods: ['GET', 'POST'])]
    public function nuevo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $paseo = new Paseo();
        $form = $this->createForm(PaseoType::class, $paseo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($paseo);
            $entityManager->flush();

            $this->addFlash('success', 'Paseo creado correctamente.');

            return $this->redirectToRoute('app_admin_paseos');
        }

        return $this->render('admin/paseo_form.html.twig', [
            'formulario' => $form,
            'es_nuevo' => true,
        ]);
    }

    #[Route('/admin/paseos/{id}', name: 'app_admin_paseo_ver', methods: ['GET'])]
    public function ver(Paseo $paseo): Response
    {
        return $this->render('admin/paseo_ver.html.twig', [
            'paseo' => $paseo,
        ]);
    }

    #[Route('/admin/paseos/{id}/editar', name: 'app_admin_paseo_editar', methods: ['GET', 'POST'])]
    public function editar(Request $request, Paseo $paseo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaseoType::class, $paseo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Paseo actualizado correctamente.');

            return $this->redirectToRoute('app_admin_paseos');
        }

        return $this->render('admin/paseo_form.html.twig', [
            'formulario' => $form,
            'es_nuevo' => false,
            'paseo' => $paseo,
        ]);
    }

    #[Route('/admin/paseos/{id}/eliminar', name: 'app_admin_paseo_eliminar', methods: ['POST'])]
    public function eliminar(Request $request, Paseo $paseo, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_csrf_token');

        if (
            !is_string($token)
            || !$this->isCsrfTokenValid('delete_paseo_' . $paseo->getId(), $token)
        ) {
            throw $this->createAccessDeniedException('Token CSRF inválido.');
        }

        $entityManager->remove($paseo);
        $entityManager->flush();

        $this->addFlash('success', 'Paseo eliminado correctamente.');

        return $this->redirectToRoute('app_admin_paseos');
    }

    #[Route('/admin/paseos/perros-por-cliente/{clienteId}', name: 'app_admin_paseos_perros_por_cliente', methods: ['GET'])]
    public function perrosPorCliente(int $clienteId, PerroRepository $repository): JsonResponse
    {
        $perros = $repository->findByPropietario($clienteId);

        $data = [];
        foreach ($perros as $perro) {
            $data[] = [
                'id' => $perro->getId(),
                'nombre' => $perro->getNombre(),
                'raza' => $perro->getRaza(),
            ];
        }

        return new JsonResponse($data);
    }
}
