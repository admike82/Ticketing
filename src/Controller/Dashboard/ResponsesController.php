<?php

namespace App\Controller\Dashboard;

use App\Entity\Ticket;
use App\Form\ResponseType;
use App\Entity\UserAccount;
use App\Repository\ResponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Response as EntityResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ResponsesController extends AbstractController
{
    #[Route('/dashboard/responses/{id}', name: 'app_dashboard_responses')]
    public function index(Ticket $ticket, ResponseRepository $responseRepo, Request $request, #[CurrentUser] ?UserAccount $user, EntityManagerInterface $em): Response
    {   
        $newResponse = new EntityResponse();
        $form = $this->createForm(ResponseType::class, $newResponse);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $newResponse->setTicket($ticket);
            $newResponse->setUserAccount($user);
            $em->persist($newResponse);
            $em->flush();
            return $this->redirectToRoute('app_dashboard_responses', ['id' => $ticket->getId()]);
        }

        $responses = $responseRepo->findBy(['ticket' => $ticket]);

        return $this->render('dashboard/responses/index.html.twig', [
            'controller_name' => 'ResponsesController',
            'responses' => $responses,
            'ticket' => $ticket,
            'form' => $form->createView()
        ]);
    }
}
