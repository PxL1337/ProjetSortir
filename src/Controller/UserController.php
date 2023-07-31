<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;


/**
 * @method User getUser()
 */
#[Route('/user')]
class UserController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    #[Route('/profile/{username}', name: 'user_profile')]
    public function profile(string $username, UserRepository $userRepository): Response
    {
        $user = $userRepository->findByUsernameWithCampus($username);

        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edition/{id}', name: 'user.edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $manager): Response
    {
        /*if (!$this->getUser()){
            return $this->redirectToRoute('security.login');
        }*/

        if ($this->getUser() !== $user){
            return $this->redirectToRoute('user_profile', ['username' => $this->getUser()->getFirstname()]);
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

        return $this->render('/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
