<?php

namespace App\Controller\Dashboard;

use App\Entity\UserAccount;
use App\Repository\TicketRepository;
use App\Repository\ApplicationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TicketsController extends AbstractController
{
    #[Route('/dashboard/tickets', name: 'app_dashboard_tickets')]
    public function index(#[CurrentUser] ?UserAccount $user, TicketRepository $ticketRepository, ApplicationRepository $applicationRepository): Response
    {
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $tickets = $ticketRepository->findAll();
        } else {
            $applications = $applicationRepository->findBy(['userAccount' => $user->getId()]);
            $ticketsApp = [];
            foreach ($applications as $application) {
                $ticketsApp = array_merge($ticketsApp, $application->getTickets()->toArray());
            }
            $ticketsUser = $ticketRepository->findBy(['userAccount' => $user->getId()]);
            $tickets = array_merge($ticketsApp, $ticketsUser);
        }
        return $this->render('dashboard/tickets/index.html.twig', [
            'tickets' => $tickets
        ]);
    }
}
