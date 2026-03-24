<?php

namespace App\Controller;
use App\Entity\Cost;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Cart;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;


final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order', methods: ['GET', 'POST'])]
    public function index(EntityManagerInterface $entityManager, Request $request, SessionInterface $session, OrderRepository $orderRepository,Cart $cart): Response
    {
        // on recupere les donnees du penier ds la session
        $data = $cart->getCart($session);
       
        // $cart = $session->get('cart',[]);
        // // Initialisation du tableau pour stocker les données du panier avec les informations
        // $cartWithData = [];
        // //boucle sur les éléments du panier pour récupérer les informations du produit
        // foreach ($cart as $id => $quantity) {
        //     //récupère le produit correspondant à l'id et la quantité
        //     $cartWithData[] = [
        //         'product'=> $productRepository->find($id), //récup le produit via son id
        //         'quantity'=> $quantity // quantité du produit dans le panier
        //     ];
        //     }//calcul total du tableau
        //     $total = array_sum(array_map(function ($item){
        //     // pour chaque elements du panier , multiplie par le prix du produit par la quantité
        //         return $item['product']->getPrice() * $item['quantity'];
        //     }, $cartWithData));
        // on créé un nouvel objet
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($order->isPayOnDelivery()){
                if(!empty($data['total'])){
                //dd($order);

                //on defini le prix total de la commande
                    $order->setTotalPrice($data['total']);
                    $order ->setCreatedAt(new \DateTimeImmutable());
                    $entityManager->persist($order);
                    $entityManager->flush();
                
                //dd($data['cart']);
                //$orderProduct = new OrderProduct();
                // $orderProduct->setOrder($order);
            
                // on boucle sur tt les elements du panier
                    foreach ($data['cart'] as $value){
                        $orderProduct = new OrderProduct();
                        // pn definit la commande pour le produit de la commande
                        $orderProduct->setOrder($order);
                        $orderProduct->setOrder($order);
                        $orderProduct->setProduct($value['product']);
                        $orderProduct->setQuantity($value['quantity']);
                        // on enregistre le produit de la commande
                        $entityManager->persist($orderProduct);
                        $entityManager->flush();
                    }
                }   
                $session->set('cart', []);
                return $this->redirectToRoute('app_order_message');
                
                }
            }

            return $this->render('order/index.html.twig', [
                'form' => $form->createView(),
                'total'=> $data['total']
            ]);
    }

    #[Route('/city/{id}/shipping/cost',name: 'app_city_shipping_cost')]
    public function cityShippingCost(Cost $city): Response
    {
        $cityShippingPrice = $city->getCost();

        return new Response(json_encode(['status'=>200, "message"=>'on', 'content'=> $cityShippingPrice]));
    }
    #[Route('/order_message', name:'app_order_message')]
    public function orderMessage():Response
    {
        return $this->render('order/order_message.html.twig');
    }
    #[Route('/editor/order', name:'app_orders_show')]
    public function getAllOrder(OrderRepository $orderRepository,Request $request, PaginatorInterface $paginator):Response
    {
         $data =$orderRepository->findBy([],['id'=>"DESC"]);
            $orders = $paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            2
    );  

        return $this->render('order/orders.html.twig', [
        'orders' => $orders,
        // 'items' => $items
    ]);
    }
}
