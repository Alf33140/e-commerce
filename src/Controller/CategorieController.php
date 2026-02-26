<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CatergoryFormType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieController extends AbstractController
{
    #[Route('/categoriecontroller', name: 'app_categorieController')]
    public function index(CategorieRepository $repo,): Response
    {
            $categories = $repo->findAll(); // find all: renvoies moi toutes les categories de la base de données
            
            return $this->render('categorieController/index.html.twig', [
                'categories' => $categories, // categories: c'est le nom de la variable que j'utilise dans mon template pour afficher les categories
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
            return $this->redirectToRoute('app_categorieController');
           
        }
        
        return $this->render('categoriecontroller/newCategorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/categorie/update/{id}', name:'app_categorie_update')]
    public function modifycategory(EntityManagerInterface $entityManager, Request $request, Categorie $category): Response
    { 
    
        $form = $this->createForm(CatergoryFormType::class, $category);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_home_page');
        }
        return $this->render('categoriecontroller/modifyCategorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/categoriecontroller/remove/{id}', name:'app_categorie_remove')]
    public function removecategory(EntityManagerInterface $entityManager, Categorie $category): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('app_categorie');
    }
}
