<?php

namespace App\Controller\Dashboard;

use App\Entity\Application;
use App\Entity\UserAccount;
use App\Form\ApplicationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ApplicationRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;

class ApplicationController extends AbstractController
{
    public function __construct(private readonly Security $security)
    {
    }

    #[Route('/dashboard/applications', name: 'app_dashboard_applications')]
    public function index(
        #[CurrentUser] ?UserAccount $user,
        ApplicationRepository $applicationRepository
    ): Response {
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $applications = $applicationRepository->findAll();
        } else {
            $applications = $applicationRepository->findBy(['userAccount' => $user->getId()]);
        }
        return $this->render("dashboard/applications.html.twig", [
            'applications' => $applications
        ]);
    }

    #[Route('/dashboard/applications/add', name: 'app_dashboard_applications_add')]
    public function addApplications(
        Request $request,
        #[CurrentUser] ?UserAccount $user,
        EntityManagerInterface $em
    ): Response|RedirectResponse {
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
    public function genToken(
        Application $application,
        #[CurrentUser] ?UserAccount $user,
        EntityManagerInterface $em
    ): RedirectResponse {
        if ($application->getUserAccount() !== $user && !$this->security->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', "l'application en vous appartient pas !");
            return $this->redirectToRoute("app_dashboard_applications");
        }

        $length = 64;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $tokenPlain = '';
        for ($i = 0; $i < $length; $i++) {
            $tokenPlain .= $characters[random_int(0, $charactersLength - 1)];
        }
        //crypter le token
        $salt = $this->getParameter('app.security.salt');
        $token = crypt($tokenPlain, $salt);

        $application->setToken($token);
        $em->persist($application);
        $em->flush();

        $this->addFlash('success', "Veuillez notez le token suivant : " . $tokenPlain . "\n Il ne sera plus consultable.");
        return $this->redirectToRoute("app_dashboard_applications");
    }
}
