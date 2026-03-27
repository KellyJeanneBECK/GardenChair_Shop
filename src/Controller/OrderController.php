<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Entity\OrderProducts;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Cart;
use App\Service\StripePayment;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class OrderController extends AbstractController
{
    public function __construct(private MailerInterface $mailer)
    {
        
    }

    #[Route('/order', name: 'app_order')]
    public function index(Request $request, EntityManagerInterface $em, SessionInterface $session, ProductRepository $productRepository, Cart $cart): Response
    {
        $data = $cart->getCart($session);

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            if(!empty($data['total'])){
                $totalPrice = $data['total'] + $order->getCity()->getShippingCost();
                $order->setTotalPrice($totalPrice);
                $order->setCreatedAt(new DateTimeImmutable());
                $order->setIsPaymentCompleted(0);

                $em->persist($order);
                $em->flush();
                
                foreach($data['cart'] as $value) {
                    $orderProduct = new OrderProducts();
                    $orderProduct->setOrder($order);
                    $orderProduct->setProduct($value['product']);
                    $orderProduct->setQuantity($value['quantity']);
                    $em->persist($orderProduct);
                    $em->flush();
                }

                if($order->isPayOnDelivery()) {
                    $session->set('cart', []);

                    $html = $this->renderView('mail/orderCorfirm.html.twig', [
                        'order'=>$order
                    ]);
                    $email = (new Email())
                    ->from('gardenchair@gmail.com')
                    ->to($order->getEmail())
                    ->subject('Confirmation of order reception')
                    ->html($html);
                    $this->mailer->send($email);

                    return $this->redirectToRoute('app_order_message');
                }
            } 

            $paymentStripe = new StripePayment();
            $shippingCost = $order->getCity()->getShippingCost();
            $paymentStripe->startPayment($data, $shippingCost, $order->getId());
            $stripeRedirectUrl = $paymentStripe->getStripeRedirectUrl();

            return $this->redirect($stripeRedirectUrl);
        }
        
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'total' => $data['total']
        ]);
    }

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function cityShippingCost(City $city): Response
    {
        $cityShippingPrice = $city->getShippingCost();

        return new Response(json_encode(['status'=>200, 'message'=>'on', 'content'=>$cityShippingPrice]));
    }

    #[Route('/order/message', name:'app_order_message')]
    public function orderMessage():Response
    {
        return $this->render('order/order_message.html.twig');
    }

    #[Route('/editor/order/{type}', name:'app_orders_show')]
    public function getAllOrder($type, OrderRepository $orderRepository, Request $request, PaginatorInterface $paginator):Response
    {
        if($type == 'is-completed'){
            $data = $orderRepository->findBy(['isCompleted'=>1], ['id'=>'DESC']);
        } else if($type == 'pay-on-stripe-not-delivered'){
            $data = $orderRepository->findBy(['isCompleted'=>null, 'payOnDelivery'=>0], ['id'=>'DESC']);
        } else if($type == 'pay-on-stripe-is-delivered'){
            $data = $orderRepository->findBy(['isCompleted'=>1, 'payOnDelivery'=>0], ['id'=>'DESC']);
        } else if($type == 'no-delivery'){
            $data = $orderRepository->findBy(['isCompleted'=>null], ['id'=>'DESC']);
        }

        $orders = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('order/order_list.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/editor/order/{id}/is-completed/update', name: 'app_orders_is-completed-update')]
    public function isCompleted($id, OrderRepository $orderRepo, EntityManagerInterface $em, Request $request): Response
    {
        $order = $orderRepo->find($id);
        $order->setIsCompleted(true);
        $em->flush();

        $this->addFlash('success', "This order is delivered");
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/editor/order/{id}/delete', name: 'app_orders_delete')]
    public function deleteOrder(EntityManagerInterface $em, Order $order, Request $request): Response
    {
        $em->remove($order);
        $em->flush();
        $this->addFlash('danger', "This order was deleted");
        return $this->redirect($request->headers->get('referer'));
    }
}