<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CatergoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieController extends AbstractController
{
    #[Route('/categoriecontroller', name: 'app_categorieController')]
    public function index(): Response
    {
            return $this->render('categorieController/index.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }
    #[Route('/categorie/new', name: 'app_categorie_new')]
    public function addcategory(EntityManagerInterface $entityManager, Request $request): Response
    {
        $category = new Categorie();
        $form = $this->createForm(CatergoryFormType::class, $category);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
      
            $entityManager->persist($category);
           
            $entityManager->flush();
           
        }
        
        return $this->render('categoriecontroller/newCategorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
