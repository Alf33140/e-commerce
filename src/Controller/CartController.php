<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    //sert a injecter dse dépendances dans notre controller, donc le repo
    //et il sera impossible de le modifier car il est en readonly
    //ca va permettre d utiliser notre repo partout ds le controller
    //sans avoir a le repasser en parametre avec $this
    public function __construct(private readonly ProductRepository $productRepository )
    {

    } 

    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        // on recupere les donnees du panier en session, ou un tableau vide si il n'y a rien
        $cart = $session->get('cart',[]);
        // Initialisation du tableau pour stocker les données du panier avec les informations
        $cartWithData = [];
        //boucle sur les éléments du panier pour récupérer les informations du produit
        foreach ($cart as $id => $quantity) {
            //récupère le produit correspondant à l'id et la quantité
            $cartWithData[] = [
                'product'=> $this->productRepository->find($id), //récup le produit via son id
                'quantity'=> $quantity // quantité du produit dans le panier
            ];
            }//calcul total du tableau
            $total = array_sum(array_map(function ($item){
            // pour chaque elements du panier , multiplie par le prix du produit par la quantité
                return $item['product']->getPrice() * $item['quantity'];
            }, $cartWithData));

           // dd($cartWithData);
           //Rendu de la vue pour afficher le panier
        return $this->render('cart/index.html.twig', [
            'items' =>$cartWithData,
            'total'=>$total
        ]);     
    
    }

    #[Route('cart/add/{id}/', name:'app_cart_new', methods: ['GET'])]

    public function addProductToCart(int $id,SessionInterface $session): Response
    // methode pour ajouter un produit au panier , prends l'id du produit et la session en parametres
    {
        $cart = $session->get('cart',[]);
        // récupere le panier actuel de la session ou du tableau vide si il n existe pas
        if (!empty($cart[$id])) {
        $cart[$id]++;
    }else{
        $cart[$id]=1;
    }
   // si le produit est deja dans le panier, incrémente sa quantité, sinon l'ajoute avec une quantité
   $session->set('cart',$cart);
   //met a jour le panier dans la session
   return $this->redirectToRoute('app_cart');// redirige vers la page du panier
    }
    #[Route('/cart/remove/{id}', name: 'app_cart_delete', methods: ['GET'])]
    public function removeToCart(int $id,SessionInterface $sessionInterface): Response 
    {
       $cart = $sessionInterface->get('cart', []);
   
        if (!empty($cart[$id])) {
        unset($cart[$id]);
        }
        $sessionInterface->set('cart',$cart);

        return $this->redirectToRoute('app_cart');// redirige vers la page du panier
    }
    #[Route('/remove/', name:'app_remove_all_cart', methods: ['GET'])]
    public function removeProductFromCart(SessionInterface $sessionInterface): Response
    {
        $sessionInterface->set('cart',[]);

        return $this->redirectToRoute('app_cart');
    }
}
