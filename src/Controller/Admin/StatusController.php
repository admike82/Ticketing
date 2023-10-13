<?php

namespace App\Controller\Admin;

use App\Repository\StatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
