<?php

namespace App\Controller;

use App\Entity\Subcategorie;
use App\Form\SubcategorieType;
use App\Repository\SubcategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/subcategorie')]
final class SubcategorieController extends AbstractController
{
    #[Route(name: 'app_subcategorie_index', methods: ['GET'])]
    public function index(SubcategorieRepository $subcategorieRepository): Response
    {
        return $this->render('subcategorie/index.html.twig', [
            'subcategories' => $subcategorieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_subcategorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subcategorie = new Subcategorie();
        $form = $this->createForm(SubcategorieType::class, $subcategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subcategorie);
            $entityManager->flush();

            return $this->redirectToRoute('app_subcategorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subcategorie/new.html.twig', [
            'subcategorie' => $subcategorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subcategorie_show', methods: ['GET'])]
    public function show(Subcategorie $subcategorie): Response
    {
        return $this->render('subcategorie/show.html.twig', [
            'subcategorie' => $subcategorie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_subcategorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Subcategorie $subcategorie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubcategorieType::class, $subcategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_subcategorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subcategorie/edit.html.twig', [
            'subcategorie' => $subcategorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subcategorie_delete', methods: ['POST'])]
    public function delete(Request $request, Subcategorie $subcategorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subcategorie->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($subcategorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_subcategorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
