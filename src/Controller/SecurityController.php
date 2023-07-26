<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        #region Variables declaration for login.html.twig
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        #endregion

        #region Custom messages declaration for login.html.twig
        // Remember me message
        $rememberMe_Message = "Remember Me";

        // Login error message
        $loginError_Message = "Incorrect username or password.";
        #endregion

        return $this->render('security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'rememberMeMessage' => $rememberMe_Message,
                'loginError_Message' => $loginError_Message
            ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
