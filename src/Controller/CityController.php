<?php

namespace App\Controller;

use App\Entity\Cost;
use App\Form\CostType;
use App\Repository\CostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/city')]
final class CityController extends AbstractController
{
    #[Route(name: 'app_city_index', methods: ['GET'])]
    public function index(CostRepository $costRepository): Response
    {
        return $this->render('city/index.html.twig', [
            'costs' => $costRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_city_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cost = new Cost();
        $form = $this->createForm(CostType::class, $cost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cost);
            $entityManager->flush();

            return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('city/new.html.twig', [
            'cost' => $cost,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_city_show', methods: ['GET'])]
    public function show(Cost $cost): Response
    {
        return $this->render('city/show.html.twig', [
            'cost' => $cost,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_city_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cost $cost, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CostType::class, $cost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('city/edit.html.twig', [
            'cost' => $cost,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_city_delete', methods: ['POST'])]
    public function delete(Request $request, Cost $cost, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cost->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cost);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
    }
}
