<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('home/index.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/home/product/{id}', name: 'app_home_product')]
    public function showProduct(Product $product): Response
    {
        return $this->render('home/show.html.twig', [
            'product' => $product
        ]);
    }
}