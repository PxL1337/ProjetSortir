<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @method User getUser()
 */
#[Route('/user')]
#[isGranted('ROLE_USER')]
class UserController extends AbstractController
{

    #[Route('/profile/{username}', name: 'user_profile')]
    public function profile(string $username, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        if ($user !== $this->getUser()) {
            return $this->redirectToRoute('user_public_profile', ['username' => $username]);
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/view/{username}', name: 'user_public_profile')]
    public function viewProfile(string $username, UserRepository $userRepository): Response
    {
        $user = $userRepository->findByUsernameWithCampus($username);

        if ($user === $this->getUser()) {
            return $this->redirectToRoute('user_profile', ['username' => $username]);
        }

        return $this->render('user/public_profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edition/{id}', name: 'user.edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->getUser() !== $user){
            return $this->redirectToRoute('user_profile', ['username' => $this->getUser()->getFirstname()]);
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get current password from form
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            // Check if current password is correct
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                // Add flash message
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');

                // Render the form again
                return $this->render('user/edit.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Check if new password and confirm password match
            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les nouveaux mots de passe ne correspondent pas.');
                // Render the form again
                return $this->render('user/edit.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Photo upload
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$photoFile->guessExtension();
                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Unable to upload photo file: '.$e->getMessage());

                    return $this->render('user/edit.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
                $user->setPhoto($newFilename);
            }

            if ($newPassword !== null) {
                // If all checks pass, update the password
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            }


            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les informations ont été mises à jour.'
            );

            return $this->redirectToRoute('user_profile', ['username' => $this->getUser()->getFirstname()]);
        }

        return $this->render('/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
