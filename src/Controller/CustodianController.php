<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketEditFormType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_CUSTODIAN")
 */
class CustodianController extends AbstractController
{
    /**
     * @Route("/custodian", name="custodian_dashboard")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository(Ticket::class)->createQueryBuilder('ticket');

        if ($request->query->getAlnum('filter')) {
            $queryBuilder->where('ticket.name LIKE :name')->setParameter('name', '%' . $request->query->getAlnum('filter') . '%');
        } else {
            $queryBuilder->orderBy('ticket.id', 'ASC');
        }

        $query = $queryBuilder->getQuery();

        $tickets = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('custodian/index.html.twig', [
            'tickets' => $tickets,
            'page' => $request->query->getInt('page', 1),
            'filter' => $request->query->getAlnum('filter')
        ]);
    }

    /**
     * @Route("/custodian/delete/{ticketId}", name="delete_ticket")
     */
    public function deleteTicket(Request $request, $ticketId)
    {
        $ticketRepository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $ticketRepository->find($ticketId);
        if ($ticket) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ticket);
            $entityManager->flush();
            $this->addFlash('success', 'Ticket successfully deleted.');
        }

        return $this->redirectToRoute('custodian_dashboard', [
                'filter' => $request->query->getAlnum('filter'),
                'page' => $request->query->getInt('page', 1)
            ]
        );
    }

    /**
     * @Route("/custodian/upvote/{ticketId}", name="upvote_ticket")
     */
    public function upvoteTicket(Request $request, $ticketId)
    {
        $ticketRepository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $ticketRepository->find($ticketId);
        if ($ticket) {
            $entityManager = $this->getDoctrine()->getManager();
            $ticket->setNumberOfVotes($ticket->getNumberOfVotes() + 1);
            $entityManager->flush();
            $this->addFlash('success', 'Ticket successfully upvoted.');
        }

        return $this->redirectToRoute('custodian_dashboard', [
            'filter' => $request->query->getAlnum('filter'),
            'page' => $request->query->getInt('page', 1)
        ]);
    }

    /**
     * @Route("/custodian/edit_ticket/{ticketId}", name="edit_ticket")
     */
    public function editTicket(Request $request, $ticketId)
    {
        $ticket = $this->getDoctrine()->getRepository(Ticket::class)->findOneBy(['id' => $ticketId]);

        if ($ticket) {
            $form = $this->createForm(TicketEditFormType::class, $ticket);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->addFlash('success', 'Ticket ' . $ticket->getName() . ' successfully edited.');

                $entityManager = $this->getDoctrine()->getManager();
                $ticket->setName($form->get("name")->getData());
                $ticket->setDescription($form->get('description')->getData());
                $entityManager->flush();

                return $this->redirectToRoute('custodian_dashboard', [
                    'created_ticket' => $ticket,
                    'filter' => $request->query->getAlnum('filter'),
                    'page' => $request->query->getInt('page', 1)
                ]);
            }

            return $this->render('ticket/edit_ticket.html.twig', [
                'edit_form' => $form->createView(),
                'ticket_name' => $ticket->getName(),
            ]);
        }

        return $this->redirectToRoute('custodian_dashboard', [
            'filter' => $request->query->getAlnum('filter'),
            'page' => $request->query->getInt('page', 1)
        ]);
    }
}
