<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order', methods: ['GET'])]
    public function index( Request $request ): Response
    {

        $order = $newOrder = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
}
