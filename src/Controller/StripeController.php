<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StripeController extends AbstractController
{
    #[Route('/pay/success', name: 'app_stripe_success')]
    public function stripeSuccess(): Response
    {
        return $this->render('stripe/success.html.twig', [
            
        ]);
    }

    #[Route('/pay/cancel', name: 'app_stripe_cancel')]
    public function stripeCancel(): Response
    {
        return $this->render('stripe/cancel.html.twig', [
            
        ]);
    }

     #[Route('/stripe/notify', name: 'app_stripe_notify', methods:['POST'])]
    public function stripeNotify(Request $request, OrderRepository $orderRepository, EntityManagerInterface $em): Response

    {
        
        Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);
        
        
        $endpoint_secret = ($_SERVER['STRIPE_WEBHOOK_SECRET']);
        
        $payload = $request->getContent();
       
        $sigHeader = $request->headers->get('Stripe-Signature');
        
        $event = null;

        try {
            
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
           
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            
            return new Response('Invalid signature', 400);
        }
        
        
        switch ($event->type) {
            case 'payment_intent.succeeded':  
                $paymentIntent = $event->data->object;
                
               
                // $fileName = 'stripe-detail-'.uniqid().'.txt';
                $orderId = $paymentIntent->metadata->orderId;
                $order = $orderRepository->find($orderId);
                $order->setIsPaymentCompleted(1);
                $em->flush();
                // file_put_contents($fileName, $orderId);

                break;
            case 'payment_method.attached':   
                $paymentMethod = $event->data->object; 
                break;
            default :
                
                break;
        }

        return new Response('Événement reçu avec succès', 200);
    }
}