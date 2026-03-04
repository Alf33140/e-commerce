<?php

namespace App\Controller;

use App\Repository\ProductRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]  #cette annotation indique que la méthode index() est associée à la route racine ("/") de l'application. Lorsque les utilisateurs accèdent à cette URL, la méthode index() sera exécutée pour générer la réponse appropriée.
    public function index(ProductRepository $productRepository): Response  #
    {
        $products = $productRepository->findAll();   #Cette ligne utilise le ProductRepository pour récupérer tous les produits de la base de données. La méthode findAll() renvoie une liste de tous les produits disponibles, qui est ensuite stockée dans la variable $products.

    
        return $this->render('home_page/index.html.twig', [
            'products' => $products,  #Cette ligne passe la variable $products à la vue Twig (home_page/index.html.twig) en tant que variable nommée 'products'. Cela permet à la vue d'accéder à la liste des produits et de les afficher de manière appropriée dans le template HTML.
            
      
        ]);
        
    }
    
    #[Route('/product', name: 'app_ShowProduct', methods: ['GET'])]
    public function showProductHomepage(): Response
    {
        
        return $this->render('home_page/index.html.twig', [
            
        ]);
        
    }
    
}
