<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
#[IsGranted('ROLE_USER')]
final class CartController extends AbstractController
{
    
    public function __construct(private readonly ProductRepository $productRepository)
    {}
    
    #[Route(name: 'app_cart', methods: ['GET'])]
    public function index(SessionInterface $session, Cart $cart): Response
    {
        $data = $cart->getCart($session);

        return $this->render('cart/index.html.twig', [
            'items' => $data['cart'],
            'total' => $data['total']
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_new', methods: ['GET'])]
    public function addProductToCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if(!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/remove/{id}', name:'app_cart_remove_product', methods: ['GET'])]
    public function removeProductFromCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if(!empty($cart[$id])) {
            unset($cart[$id]);
        }
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/remove', name:'app_cart_remove', methods: ['GET'])]
    public function removeCart(SessionInterface $session): Response
    {
        $session->set('cart', []);
        return $this->redirectToRoute('app_cart');
    }
}