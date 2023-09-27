<?php

namespace App\Controller\Dashboard;

use App\Entity\UserAccount;
use App\Repository\ApplicationRepository;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class DashboardController extends AbstractController

{
    #[Route('/dashboard', name: 'app_dashboard')]
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
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'tickets' => $tickets
        ]);
    }
}
