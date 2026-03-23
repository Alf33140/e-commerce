<?php

namespace App\Service;

use App\Repository\ProductRepository;


class Cart{

public function __construct(private readonly ProductRepository $productRepository)
{
        

}
public function getCart($session): array
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

           //Rendu de la vue pour afficher le panier
        return[
            'cart' =>$cartWithData,
            'total'=>$total
        ];            
}
}