<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchEngineController extends AbstractController
{
    #[Route('/search/engine', name: 'app_search_engine', methods: ['POST'])]
    public function search(Request $request, ProductRepository $productRepository): Response
    {
        $query = $request->request->get('p');
        $products =[];

        if($query) {

            $products = $productRepository->findByProduct($query);
        }
        
    return $this->render('search_engine/index.html.twig', [
        'products' => $products,
        'search' => $query
    ]);
    }
   

    
}
