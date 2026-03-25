<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Dompdf\Options;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BillController extends AbstractController
{
    #[Route('/editor/order/{id}/bill', name: 'app_bill')]
    public function index($id, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);

        $pdfOptions = new Options(); //definit la nouvelle instanciation de new option
        $pdfOptions->set('defaultFont','Arial');// on définit le style de font en arial
        $domPdf = new Dompdf($pdfOptions);//instanciation de la class pdf et on lui ajoute les options de la font codé au dessus
        $html = $this->renderView('bill/index.html.twig', [// on precise qu on va rendre la commande pour donner la facture
            'order' =>$order
        ]);
        $domPdf->loadHtml($html);// on charge le html pour le pdf
        $domPdf->render();// on créé le rendu
        $domPdf->stream('bill-'.$order->getId().'.pdf',[// on concatene la facture pour afficher un pdf
            'attachment'=>false //on va permettre de voir et imprimer la facture 
        ]);
        // return $this->render('bill/index.html.twig', [ //plus utilisé car on a a jouté le code pour la creation du PDF
        //     'order' => $order,
        // ]);
            return new Response('',200,[ //
                'content-Type' => 'application/pdf'
            ]);
    }
}
