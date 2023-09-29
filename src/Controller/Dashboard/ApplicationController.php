<?php

namespace App\Controller\Dashboard;

use App\Entity\Application;
use App\Entity\UserAccount;
use App\Form\ApplicationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ApplicationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApplicationController extends AbstractController
{
    #[Route('/dashboard/applications', name: 'app_dashboard_applications')]
    public function index(#[CurrentUser] ?UserAccount $user, ApplicationRepository $applicationRepository)
    {
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $applications = $applicationRepository->findAll();
        } else {
            $applications = $applicationRepository->findBy(['userAccount' => $user->getId()]);
        }
        return $this->render("dashboard/applications.html.twig", [
            'applications' => $applications
        ]);
    }

    #[Route('/dashboard/applications/add', name: 'app_dashboard_applications_add')]
    public function addApplications(Request $request, #[CurrentUser] ?UserAccount $user, EntityManagerInterface $em)
    {
        $application = new Application();

        $form = $this->createForm(ApplicationType::class, $application);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $application->setUserAccount($user);
            $em->persist($application);
            $em->flush();
            $this->addFlash('success', "L'application a bien été crée !");
            return $this->redirectToRoute('app_dashboard_applications');
        }

        return $this->render("dashboard/addApplication.html.twig", [
            'form' => $form->createView()
        ]);
    }

    #[Route('/dashboard/applications/genToken/{id}', name: 'app_dashboard_applications_genToken')]
    public function genToken(Application $application, #[CurrentUser] ?UserAccount $user, EntityManagerInterface $em)
    {
        if ($application->getUserAccount() !== $user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('danger', "l'application en vous appartient pas !");
            return $this->redirectToRoute("app_dashboard_applications");
        }

        $length = 10;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[random_int(0, $charactersLength - 1)];
        }
        $application->setToken($token);
        $em->persist($application);
        $em->flush();

        $this->addFlash('success', "Veuillez notez le token suivant : " . $token . "\n Il ne sera plus consultable.");
        return $this->redirectToRoute("app_dashboard_applications");
    }
}