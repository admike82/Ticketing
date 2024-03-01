<?php

namespace App\Controller\Admin;

use App\Entity\Level;
use App\Form\LevelType;
use App\Repository\LevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/admin/levels/add', name: 'app_admin_levels_add')]
    public function add(
        Request $request,
        EntityManagerInterface $em
    ): Response {

        $level = new Level();

        $form = $this->createForm(LevelType::class, $level);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($level);
            $em->flush();
            $this->addFlash('success', "Le level a bien été crée !");
            return $this->redirectToRoute('app_admin_levels');
        }


        return $this->render('admin/levels/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
