<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EDITOR')]
final class BillController extends AbstractController
{
    #[Route('/editor/order/{id}/bill', name: 'app_bill')]
    public function index($id, OrderRepository $orderRepo): Response
    {
        $order = $orderRepo->find($id);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $domPdf = new Dompdf($pdfOptions);
        $html = $this->renderView('bill/index.html.twig', [
            'order' => $order
        ]);
        $domPdf->loadHtml($html);
        $domPdf->render();
        $domPdf->stream('GardenChair-Bill-'.$order->getId().'.pdf', [
            'Attachment' => false
        ]);
        exit;

        return new Response('', 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}