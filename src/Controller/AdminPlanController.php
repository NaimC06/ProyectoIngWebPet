<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Form\PlanType;
use App\Repository\PlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class AdminPlanController extends AbstractController
{
    #[Route('/admin/planes', name: 'app_admin_planes', methods: ['GET'])]
    public function index(PlanRepository $repository): Response
    {
        $planes = $repository->findBy([], ['id' => 'ASC']);

        return $this->render('admin/planes.html.twig', [
            'planes' => $planes,
        ]);
    }

    #[Route('/admin/planes/nuevo', name: 'app_admin_plan_nuevo', methods: ['GET', 'POST'])]
    public function nuevo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $plan = new Plan();
        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($plan);
            $entityManager->flush();

            $this->addFlash('success', 'Plan creado correctamente.');

            return $this->redirectToRoute('app_admin_planes');
        }

        return $this->render('admin/plan_form.html.twig', [
            'formulario' => $form,
            'es_nuevo' => true,
        ]);
    }

    #[Route('/admin/planes/{id}', name: 'app_admin_plan_ver', methods: ['GET'])]
    public function ver(Plan $plan): Response
    {
        return $this->render('admin/plan_ver.html.twig', [
            'plan' => $plan,
        ]);
    }

    #[Route('/admin/planes/{id}/editar', name: 'app_admin_plan_editar', methods: ['GET', 'POST'])]
    public function editar(Request $request, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Plan actualizado correctamente.');

            return $this->redirectToRoute('app_admin_planes');
        }

        return $this->render('admin/plan_form.html.twig', [
            'formulario' => $form,
            'es_nuevo' => false,
            'plan' => $plan,
        ]);
    }

    #[Route('/admin/planes/{id}/eliminar', name: 'app_admin_plan_eliminar', methods: ['POST'])]
    public function eliminar(Request $request, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_csrf_token');

        if (
            !is_string($token)
            || !$this->isCsrfTokenValid('delete_plan_' . $plan->getId(), $token)
        ) {
            throw $this->createAccessDeniedException('Token CSRF inválido.');
        }

        $entityManager->remove($plan);
        $entityManager->flush();

        $this->addFlash('success', 'Plan eliminado correctamente.');

        return $this->redirectToRoute('app_admin_planes');
    }
}
