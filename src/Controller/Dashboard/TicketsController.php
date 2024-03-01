<?php

namespace App\Controller\Dashboard;

use App\Entity\Level;
use App\Entity\Status;
use App\Entity\Ticket;
use App\Form\TicketType;
use App\Entity\UserAccount;
use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TicketsController extends AbstractController
{
    public function __construct(private readonly Security $security)
    {
    }

    #[Route('/dashboard/tickets', name: 'app_dashboard_tickets')]
    public function index(
        #[CurrentUser] ?UserAccount $user,
        TicketRepository $ticketRepository,
        ApplicationRepository $applicationRepository
    ): Response {
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
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

    #[Route('/dashboard/tickets/create', name: 'app_dashboard_tickets_create')]
    public function create(
        Request $request,
        #[CurrentUser] ?UserAccount $user,
        EntityManagerInterface $em,
        StatusRepository $statusRepository
    ) {
        $newTicket = new Ticket();
        $form = $this->createForm(TicketType::class, $newTicket);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $status = $statusRepository->findOneBy(['name' => 'Nouveaux']);
            $newTicket->setStatus($status);
            $newTicket->setUserAccount($user);
            $em->persist($newTicket);
            $em->flush();
            return $this->redirectToRoute('app_dashboard_responses', ['id' => $newTicket->getId()]);
        }
        return $this->render('dashboard/tickets/create.html.twig', [
            'controller_name' => 'TicketsController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/dashboard/tickets/statuses/{id}', name: 'app_dashboard_tickets_statuses')]
    public function statuses(
        Status $status,
        #[CurrentUser] ?UserAccount $user,
        TicketRepository $ticketRepository,
        ApplicationRepository $applicationRepository
    ) {
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
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
    public function levels(
        Level $level,
        #[CurrentUser] ?UserAccount $user,
        StatusRepository $statusRepository,
        TicketRepository $ticketRepository,
        ApplicationRepository $applicationRepository
    ) {
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        $statusExculde = $statusRepository->findBy(['close' => true]);
        if ($this->security->isGranted('ROLE_ADMIN')) {
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

    #[Route('/dashboard/tickets/close/{id}', name: 'app_dashboard_tickets_close')]
    public function close(
        Ticket $ticket,
        EntityManagerInterface $em,
        StatusRepository $statusRepository
    ) {
        $status = $statusRepository->findOneBy(['name' => 'Cloturés']);
        $ticket->setStatus($status);
        $em->flush();
        return $this->redirectToRoute('app_dashboard');
    }
}
