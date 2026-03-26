<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}