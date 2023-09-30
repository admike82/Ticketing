<?php

namespace App\Controller;

use App\Entity\UserAccount;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/', name: 'app_login')]
    public function index(#[CurrentUser] ?UserAccount $user,AuthenticationUtils $authenticationUtils): Response
    {
        // $salt = $this->getParameter("app.security.salt");
        // $testCrypt = crypt('123456789azerty!', $salt);
        // dd($testCrypt);
        if ($user !== null) {
            return $this->redirectToRoute('app_dashboard');
        }
		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();
	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();
	    
        return $this->render('login/index.html.twig', [
	        'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
