<?php

namespace App\Presentation\Api\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FakePaymentController extends AbstractController
{
    private const SUCCESS_CARD = '1111-2222-3333-4444';

    public function index(Request $request): Response
    {
        $total = $request->get('total');
        $redirectUrl = $request->get('redirectUrl');
        return $this->render('fakepayment.html.twig', [
            'total' => $total,
            'redirect_url' => $redirectUrl
        ]);
    }

    public function submit(Request $request): Response
    {
        $creditNumber = $request->get('credit-number');
        $redirectUrl = $request->get('redirectUrl');
        $status = 0;

        if ($creditNumber === self::SUCCESS_CARD) {
            $status = 1;
        }

        $redirectUrl .= "?status=$status";

        return $this->redirect($redirectUrl);
    }

    public function refund(LoggerInterface $logger): Response
    {
        $logger->info('A refund sikeres');

        return new Response('A refund sikeres');
    }
}
