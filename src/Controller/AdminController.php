<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function ensureUserIsNoAdmin(User $user): void
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('error', 'You cannot delete an admin user.');
            throw new AccessDeniedException('You cannot delete an admin user.');
        }
    }

    #[Route('/', name: 'dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/list-user', name: 'list_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function list(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAllWithRoles();

        return $this->render('admin/list_user.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/modify_user/{id}', name: 'modify_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('admin_list_user');
        }

        return $this->render('admin/modify_user.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete_user/{id}', name: 'delete_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(User $user): Response
    {
        $this->ensureUserIsNoAdmin($user);

        return $this->render('admin/delete_user.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/confirm_delete/{id}', name: 'confirm_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function confirmDelete(Request $request, User $user): Response
    {
        $this->ensureUserIsNoAdmin($user);

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'User deleted successfully.');
        }

        return $this->redirectToRoute('admin_list_user');
    }

    #[Route('/city/list', name: 'city_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function listCity(): Response
    {
        return $this->render('admin/city_list.html.twig');
    }
}
