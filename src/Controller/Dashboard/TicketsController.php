<?php

namespace App\Controller\Dashboard;

use App\Entity\Level;
use App\Entity\Status;
use App\Entity\UserAccount;
use App\Repository\TicketRepository;
use App\Repository\ApplicationRepository;
use App\Repository\StatusRepository;
use Doctrine\Common\Collections\Criteria;
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
    public function levels(Level $level, #[CurrentUser] ?UserAccount $user,StatusRepository $statusRepository, TicketRepository $ticketRepository, ApplicationRepository $applicationRepository)
    {
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        $statusExculde = $statusRepository->findBy(['close' => true]);
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $criteriaAdmin = Criteria::create()
                ->andWhere(Criteria::expr()->notIn('status', $statusExculde))
                ->andWhere(Criteria::expr()->eq('level', $level));
            $tickets = $ticketRepository->matching($criteriaAdmin);
        } else {
            $applications = $applicationRepository->findBy(['userAccount' => $user->getId()]);
            $criteriaUser = Criteria::create()
                ->andWhere(Criteria::expr()->in('application', $applications))
                ->orWhere(Criteria::expr()->eq('userAccount', $user))
                ->andWhere(Criteria::expr()->notIn('status', $statusExculde))
                ->andWhere(Criteria::expr()->eq('level', $level));
            $tickets = $ticketRepository->matching($criteriaUser);
        }
        return $this->render('dashboard/tickets/index.html.twig', [
            'tickets' => $tickets
        ]);
    }
}
