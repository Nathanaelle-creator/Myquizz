<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // gerrer les erreurs
        $error = $authenticationUtils->getLastAuthenticationError();

        // ladernier ursname (email)
        $lastUsername = $authenticationUtils->getLastUsername();

           if ($this->getUser()) {
            return $this->redirectToRoute('app_account');
        }
        return $this->render('login/login.html.twig', [
            'error'=>$error,
            'last_username'=>$lastUsername,
            'test_token'=>"1234"
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
