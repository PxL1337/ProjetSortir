<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/profile/{username}', name: 'user_profile')]
    public function profile(string $username, UserRepository $userRepository): Response
    {
        $userWithCampus = $userRepository->findByUsernameWithCampus($username);

        return $this->render('user/profile.html.twig', [
            'user' => $userWithCampus,
        ]);
    }
}
