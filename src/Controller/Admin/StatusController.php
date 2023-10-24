<?php

namespace App\Controller\Admin;

use App\Entity\Status;
use App\Form\StatusType;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatusController extends AbstractController
{
    #[Route('/admin/status', name: 'app_admin_status')]
    public function index(StatusRepository $statusRepo): Response
    {
        $statuses = $statusRepo->findAll();
        return $this->render('admin/status/index.html.twig', [
            'controller_name' => 'StatusController',
            'statuses' => $statuses
        ]);
    }

    #[Route('/admin/status/add', name: 'app_admin_status_add')]
    public function add(Request $request, EntityManagerInterface $em): Response {

        $status = new Status();

        $form = $this->createForm(StatusType::class, $status);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($status);
            $em->flush();
            $this->addFlash('success', "Le statu a bien été crée !");
            return $this->redirectToRoute('app_admin_status');
        }
        

        return $this->render('admin/status/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
