<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;


final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order', methods: ['GET'])]
    public function index( Request $request, SessionInterface $session,ProductRepository $productRepository ): Response
    {
        $cart = $session->get('cart',[]);
        // Initialisation du tableau pour stocker les données du panier avec les informations
        $cartWithData = [];
        //boucle sur les éléments du panier pour récupérer les informations du produit
        foreach ($cart as $id => $quantity) {
            //récupère le produit correspondant à l'id et la quantité
            $cartWithData[] = [
                'product'=> $productRepository->find($id), //récup le produit via son id
                'quantity'=> $quantity // quantité du produit dans le panier
            ];
            }//calcul total du tableau
            $total = array_sum(array_map(function ($item){
            // pour chaque elements du panier , multiplie par le prix du produit par la quantité
                return $item['product']->getPrice() * $item['quantity'];
            }, $cartWithData));

        $order = $newOrder = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'total'=> $total
        ]);
    }

    
}
