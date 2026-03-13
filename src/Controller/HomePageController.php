<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategorieRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]  //cette annotation indique que la méthode index() est associée à la route racine ("/") de l'application. Lorsque les utilisateurs accèdent à cette URL, la méthode index() sera exécutée pour générer la réponse appropriée.
    public function index(ProductRepository $productRepository, CategorieRepository $categorieRepository): Response 
    {
        $products = $productRepository->findAll();   

    
        return $this->render('home_page/index.html.twig', [
            'products' => $products,  //Cette ligne passe la variable $products à la vue Twig (home_page/index.html.twig) en tant que variable nommée 'products'. Cela permet à la vue d'accéder à la liste des produits et de les afficher de manière appropriée dans le template HTML.
            'categorie' => $categorieRepository->findAll()
      
        ]);
        
    }
    
    #[Route('/product/{id}/show', name:'app_home_product_show', methods: ['GET'])] 
    public function showProduct(Product $product, productRepository $productRepository, CategorieRepository $categorieRepository): Response
    {
        
        $lastProductsAdded = $productRepository->findBy([],['id'=> 'DESC'],5);

        return $this->render('home_page/show.html.twig', [
            'product'=> $product,
            'lastProductAdded'=> $lastProductsAdded,
            'categories' => $categorieRepository->findAll()
        ]);
    }
    
}