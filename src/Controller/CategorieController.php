<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CatergoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieController extends AbstractController
{
    #[Route('/categoriecontroller', name: 'app_categorieController')]
    public function index(): Response
    {
        $category = new Categorie();

        return $this->render('categorieController/index.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }
    #[Route('/categorie/new', name: 'app_categoriecontroller_new')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(CatergoryFormType::class);
        $form->handleRequest($request);

        return $this->render('categorieController/newCategorie.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }
}
