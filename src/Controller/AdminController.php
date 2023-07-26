<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\User;
use App\Form\AdminUserType;
use App\Form\CityType;
use App\Form\UserFilterType;
use App\Repository\CityRepository;
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
    public function list(UserRepository $userRepository, Request $request): Response
    {
        $filterForm = $this->createForm(UserFilterType::class);
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $filters = $filterForm->getData();
            $users = $userRepository->findAllWithRolesAndFilters($filters);
        } else {
            $users = $userRepository->findAllWithRoles();
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/user/_list_user_table.html.twig', [
                'users' => $users,
            ]);
        }

        return $this->render('admin/user/list_user.html.twig', [
            'users' => $users,
            'filter_form' => $filterForm->createView(),
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

        return $this->render('admin/user/modify_user.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete_user/{id}', name: 'delete_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(User $user): Response
    {
        $this->ensureUserIsNoAdmin($user);

        return $this->render('admin/user/delete_user.html.twig', [
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
    public function listCity(CityRepository $cityRepository): Response
    {
        $cities = $cityRepository->findAll();

        return $this->render('admin/city/city_list.html.twig', [
            'cities' => $cities,
        ]);
    }

    #[Route('/city/add', name: 'city_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function addCity(Request $request): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($city);
            $this->entityManager->flush();

            $this->addFlash('success', 'City added successfully.');

            return $this->redirectToRoute('admin_city_list');
        }

        return $this->render('admin/city/city_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/city/edit/{id}', name: 'city_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function editCity(City $city, Request $request): Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'City updated successfully.');

            return $this->redirectToRoute('admin_city_list');
        }

        return $this->render('admin/city/city_edit.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/city/delete/{id}', name: 'city_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteCity(City $city): Response
    {
        return $this->render('admin/city/city_delete.html.twig', [
            'city' => $city
        ]);
    }

    #[Route('/city/confirm-delete/{id}', name: 'city_confirm_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function confirmDeleteCity(Request $request, City $city): Response
    {
        if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($city);
            $this->entityManager->flush();
            $this->addFlash('success', 'City deleted successfully.');
        }

        return $this->redirectToRoute('admin_city_list');
    }

}
