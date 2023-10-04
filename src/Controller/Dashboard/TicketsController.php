<?php

namespace App\Controller\Dashboard;

use App\Entity\Level;
use App\Entity\Status;
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

            $ticketsApp = $ticketRepository->findBy(['application' => $applications]);
            $ticketsUser = $ticketRepository->findBy(['userAccount' => $user->getId()]);
            $tickets = array_merge($ticketsApp, $ticketsUser);
        }
        return $this->render('dashboard/tickets/index.html.twig', [
            'tickets' => $tickets
        ]);
    }

    #[Route('/dashboard/tickets/statuses/{id}', name: 'app_dashboard_tickets_statuses')]
    public function statuses(Status $status, #[CurrentUser] ?UserAccount $user, TicketRepository $ticketRepository, ApplicationRepository $applicationRepository)
    {
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $tickets = $ticketRepository->findBy(['status' => $status]);
        } else {
            $applications = $applicationRepository->findBy(['userAccount' => $user->getId()]);
            $ticketsApp = $ticketRepository->findBy(['application' => $applications, 'status' => $status]);
            $ticketsUser = $ticketRepository->findBy(['userAccount' => $user->getId(), 'status' => $status]);
            $tickets = array_merge($ticketsApp, $ticketsUser);
        }
        return $this->render('dashboard/tickets/index.html.twig', [
            'tickets' => $tickets
        ]);
    }

    #[Route('/dashboard/tickets/levels/{id}', name: 'app_dashboard_tickets_levels')]
    public function levels(Level $level, #[CurrentUser] ?UserAccount $user, TicketRepository $ticketRepository, ApplicationRepository $applicationRepository)
    {
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $tickets = $ticketRepository->findBy(['level' => $level, 'status' => [1, 2]]);
        } else {
            $applications = $applicationRepository->findBy(['userAccount' => $user->getId()]);
            $ticketsApp = $ticketRepository->findBy(['application' => $applications, 'level' => $level, 'status' => [1, 2]]);
            $ticketsUser = $ticketRepository->findBy(['userAccount' => $user->getId(), 'level' => $level, 'status' => [1, 2]]);
            $tickets = array_merge($ticketsApp, $ticketsUser);
        }
        return $this->render('dashboard/tickets/index.html.twig', [
            'tickets' => $tickets
        ]);
    }
}
