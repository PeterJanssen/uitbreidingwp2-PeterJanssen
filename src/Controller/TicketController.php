<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\Ticket;
use App\Form\TicketCreationFormType;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    /**
     * @Route("/register_ticket", name="register_ticket")
     */
    public function registerTicket(Request $request)
    {
        $assetId = $request->get('assetId');
        $asset = $this->getDoctrine()->getRepository(Asset::class)->findOneBy(['id' => $assetId]);
        $assetName = $asset->getName();

        $ticket = new Ticket();
        $form = $this->createForm(TicketCreationFormType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Ticket successfully created.');
            $ticket->setAsset($asset);
            $ticket->setDescription($form->get('description')->getData());
            $ticket->setName($form->get("name")->getData());
            $ticket->setCreationDate(new DateTimeImmutable());
            $ticket->setNumberOfVotes(0);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('home', [
                'created_ticket' => $ticket,
            ]);
        }

        return $this->render('ticket/register_ticket.html.twig', [
            'register_form' => $form->createView(),
            'asset_name' => $assetName,
        ]);
    }
}
