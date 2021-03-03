<?php

namespace App\Controller;

use App\Service\TicketRegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index(TicketRegistrationService $ticketRegistrationService)
    {
        $assetsForUser = $ticketRegistrationService->getAvailableAssetsForCurrentUser($this->isGranted('ROLE_USER'));
        return $this->render('home/index.html.twig', [
            'assets' => $assetsForUser,
        ]);
    }
}
