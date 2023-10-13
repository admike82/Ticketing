<?php

namespace App\Controller\Admin;

use App\Repository\LevelRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LevelsController extends AbstractController
{
    #[Route('/levels', name: 'app_admin_levels')]
    public function index(LevelRepository $levelrepo): Response
    {
        $levels = $levelrepo->findAll();
        return $this->render('admin/levels/index.html.twig', [
            'controller_name' => 'LevelsController',
            'levels' => $levels
        ]);
    }
}
