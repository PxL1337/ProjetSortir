<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;



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

    #[Route('/edition/{id}', name: 'user.edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()){
            return $this->redirectToRoute('security.login');
        }

        if ($this->getUser() !== $user){
            return $this->redirectToRoute('user_profile');
        }

        $form = $this->createForm(UserType::class, $user);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $manager ->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les informations '
            );


        }


        $form = $this->createForm(UserType::class, $user);

        return $this->render('/user/edit.html.twig', [
            'form' => $form->createView(),

        ]);
    }

}
