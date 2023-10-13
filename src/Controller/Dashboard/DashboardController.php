<?php

namespace App\Controller\Dashboard;

use App\Entity\UserAccount;
use App\Repository\LevelRepository;
use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use App\Repository\ApplicationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController

{
    public function __construct(private readonly Security $security)
    {
    }
    
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(#[CurrentUser] ?UserAccount $user, TicketRepository $ticketRepository, LevelRepository $levelRepo, StatusRepository $statusRepository, ApplicationRepository $applicationRepository): Response
    {
        $levels = $levelRepo->findAll();
        $statuses = $statusRepository->findAll();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
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
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'tickets' => $tickets,
            'statuses' => $statuses,
            'levels' => $levels
        ]);
    }
}
