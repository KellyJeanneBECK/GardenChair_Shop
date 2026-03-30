<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchEngineController extends AbstractController
{
    #[Route('/search/engine', name: 'app_search_engine', methods:['GET', 'POST'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $search = [];

        if($request->isMethod('POST')) {
            $query = $request->request->get('query');

            if ($query) {
                $search = $productRepository->searchEngine($query);
            }
        }
        
        return $this->render('search_engine/index.html.twig', [
            'products' => $search
        ]);
    }
}