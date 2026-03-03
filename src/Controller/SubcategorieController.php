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
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_ADMIN')]  # Restreint l'accès à toutes les actions du contrôleur aux utilisateurs ayant le rôle ROLE_ADMIN
final class SubcategorieController extends AbstractController
{
    #[Route('/subcategorie/index', name: 'app_subcategorie_index', methods: ['GET'])]
    public function index(SubcategorieRepository $subcategorieRepository): Response
    {
        return $this->render('subcategorie/index.html.twig', [
            'subcategories' => $subcategorieRepository->findAll(),
        ]);
    }

    #[Route('/subcategorie/new', name: 'app_subcategorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subcategorie = new Subcategorie();
        $form = $this->createForm(SubcategorieType::class, $subcategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subcategorie);
            $entityManager->flush();
            $this->addFlash('success','La sous-catégorie a été créée avec succès.');
            return $this->redirectToRoute('app_subcategorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subcategorie/new.html.twig', [
            'subcategorie' => $subcategorie,
            'form' => $form,
        ]);
    }

    #[Route('/subcategorie/{id}', name: 'app_subcategorie_show', methods: ['GET'])]
    public function show(Subcategorie $subcategorie): Response
    {
        return $this->render('subcategorie/show.html.twig', [
            'subcategorie' => $subcategorie,
        ]);
    }

    #[Route('/subcategorie/{id}/edit', name: 'app_subcategorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Subcategorie $subcategorie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubcategorieType::class, $subcategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success','La sous-catégorie a été modifiée avec succès.');
            return $this->redirectToRoute('app_subcategorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subcategorie/edit.html.twig', [
            'subcategorie' => $subcategorie,
            'form' => $form,
        ]);
    }

    #[Route('/subcategorie/{id}', name: 'app_subcategorie_delete', methods: ['POST'])]
    public function delete(Request $request, Subcategorie $subcategorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subcategorie->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($subcategorie);
            $entityManager->flush();
            $this->addFlash('success','La sous-catégorie a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_subcategorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
