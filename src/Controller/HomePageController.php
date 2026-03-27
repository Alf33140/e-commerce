<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategorieRepository;
use App\Repository\ProductRepository;
use App\Repository\SubcategorieRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]  //cette annotation indique que la méthode index() est associée à la route racine ("/") de l'application. Lorsque les utilisateurs accèdent à cette URL, la méthode index() sera exécutée pour générer la réponse appropriée.
    public function index(ProductRepository $productRepository, CategorieRepository $categorieRepository,Request $request, PaginatorInterface $paginator): Response 
    {
            $search = $productRepository->searchEngine('Pistolet');
            dd($search);
            $data =$productRepository->findBy([],['id'=>"DESC"]);
            $products = $paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            4
    );  
    
        return $this->render('home_page/index.html.twig', [
            'products' => $products, 
            'categories' => $categorieRepository->findAll()
      
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

    #[Route('/product/subcategory/{id}/filter', name: 'app_home_product_filter', methods: ['GET'])] 
    public function filter($id, SubcategorieRepository $subCategorieReposity, CategorieRepository $categorieRepository): Response
    
    {
        $product = $subCategorieReposity->find($id)->getProducts();
        $subCategory = $subCategorieReposity->find($id);
        return $this->render('home_page/filter.html.twig', [
        'products'=> $product,
        'subCategory'=> $subCategory,
        'categories'=> $categorieRepository->findAll()
        ]);
    }
}