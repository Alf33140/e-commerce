<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }
    #[Route('/pay/success', name: 'app_stripe_success')]
    public function PaySuccess(SessionInterface $session): Response
    {
        $session->set('cart',[]);
        return $this->render('stripe/paySuccess.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }
    #[Route('/pay/cancel', name: 'app_stripe_cancel')]
    public function PayCancel(): Response
    {
        return $this->render('stripe/payCancel.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }
    #[Route('/stripe/notify', name: 'app_stripe_notify')]
    public function stripeNotify(Request $request, OrderRepository $orderRepository,EntityManagerInterface $entityManager): Response
    {
        Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);
            // definir la clé secrete du webhook de stripe
        $endpoint_secret =($_SERVER['STRIPE_WEBHOOK_SECRET']);
            //recuperer le contenu de la requete
        $payload = $request->getContent();
            //récuperer l entete de signature de la requete
        $sigHeader = $request->headers->get('Stripe-Signature');
            //initialiser l'evenement a null
        $event = null;

        try{

        $event = \Stripe\Webhook::constructEvent(
            $payload, $sigHeader, $endpoint_secret
        );
        } catch (\UnexpectedValueException $e){

        return new Response('Invalid payload', 400);

        } catch (\stripe\Exception\SignatureVerificationException $e) {

        return new Response('Invalid signature', 400);
        }
        // gerer les differents types d'evenements
        switch ($event->type){
            case 'payment_intent.succeeded':// evenement de paiement reussi

                $paymentIntent = $event->data->object; //recuperer l objet payment_intent

                $fileName = 'stripe-detail-' .uniqid().'txt'; // enregistrer les dtails de paiement ds un fichier
                $orderId = $paymentIntent->metadata->orderId;
                $order = $orderRepository->find($orderId);
                $order->setIsPaymentCompleted(1);
                $entityManager->flush();

                file_put_contents($fileName,$orderId);
                break;
            case 'payment_method.attached': // evenement de methode de paiement attachée
                $paymentMethod = $event->data->object; //recuperer l'objet payment_method
                break;
            default:
                break;

        }// retourner une reponse 200 pour indiquer que l evenement a ete recu avec success
        return new Response('evenement reçu avec succes, 200');
    }
}
