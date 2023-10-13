<?php

namespace App\Controller\Admin;

use App\Form\UserType;
use App\Entity\UserAccount;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserAccountRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_admin_user')]
    public function index(UserAccountRepository $userAccountRepository): Response
    {
        $users = $userAccountRepository->findAll();

        return $this->render('admin/user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users
        ]);
    }

    #[Route('/admin/user/create', name: 'app_admin_user_create')]
    public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new UserAccount();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPlainPassword()
            );
            $user->setPassword($hashedPassword);
            $user->eraseCredentials();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_admin_user');
        }

        return $this->render('admin/user/create.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView()
        ]);
    }
}
