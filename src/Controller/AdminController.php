<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Place;
use App\Entity\User;
use App\Form\AdminUserType;
use App\Form\CampusType;
use App\Form\CityType;
use App\Form\PlaceType;
use App\Form\SearchType;
use App\Form\UserFilterType;
use App\Model\SearchData;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use App\Repository\PlaceRepository;
use App\Repository\RoleRepository;
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

    #[Route('/dashboard', name: 'dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(
        Request          $request,
        UserRepository   $userRepository,
        CityRepository   $cityRepository,
        CampusRepository $campusRepository,
        RoleRepository   $roleRepository): Response
    {
        $filterForm = $this->createForm(UserFilterType::class);
        $filterForm->handleRequest($request);

        // Factorisable en getCityContent
        $citiesSearchCityForm = $this->createForm(SearchType::class);
        $citiesSearchCityForm->handleRequest($request);
        $searchInput = $citiesSearchCityForm->get('input')->getData();

        if ($citiesSearchCityForm->isSubmitted() && $citiesSearchCityForm->isValid()) {
            $cities = array_values($this->getCityComponents( $cityRepository, $searchInput))[1];
        }
        else {
            $cities = array_values($this->getCityComponents( $cityRepository, null))[1];
        }

        dump("Cities has been set");

        $campuses = $campusRepository->findAll();
        $roles = $roleRepository->findAll();

        // Factorisable en getUserContent
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $filters = $filterForm->getData();
            $users = $userRepository->findAllWithRolesAndFilters($filters);
        } else {
            $users = $userRepository->findAllWithRoles();
        }

        dump($cities);

        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
            'filter_form' => $filterForm->createView(),
            'cities' => $cities,
            'search_city_form' => $citiesSearchCityForm->createView(),
            'campuses' => $campuses,
            'roles' => $roles,
        ]);
    }

    public function getCityComponents(CityRepository $cityRepository, ?string $searchInput): array
    {
        $searchData = new SearchData();
        $searchData->input = $searchInput;

        $searchCityForm = $this->createForm(SearchType::class, $searchData);

        $cities = $searchInput ?
            $cityRepository->findBySearch($searchData) : $cityRepository->findAll();

        return [
            'searchCityForm' => $searchCityForm,
            'cities' => $cities
        ];
    }

    #[Route('/user/modify/{id}', name: 'modify_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/user/modify_user.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/delete/{id}', name: 'delete_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(User $user): Response
    {
        $this->ensureUserIsNoAdmin($user);

        return $this->render('admin/user/delete_user.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/user/confirm_delete/{id}', name: 'confirm_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function confirmDelete(Request $request, User $user): Response
    {
        $this->ensureUserIsNoAdmin($user);

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            // Check if user's photo is not the default photo
            if ($user->getPhoto() !== 'default-photo.jpg') {
                // Get photo file path
                $photoFilePath = $this->getParameter('photos_directory').'/'.$user->getPhoto();

                // Check if file exists and delete
                if (file_exists($photoFilePath)) {
                    unlink($photoFilePath);
                }
            }

            $this->entityManager->remove($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'User deleted successfully.');
        }

        return $this->redirectToRoute('admin_dashboard');
    }


    #[Route('/campus/add', name: 'campus_new', methods: ['GET', 'POST'])]
    public function addCampus(Request $request, EntityManagerInterface $entityManager): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($campus);
            $entityManager->flush();

            return $this->redirectToRoute('admin_campus_index');
        }

        return $this->render('admin/campus/new.html.twig', [
            'campus' => $campus,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/campus/edit/{id}', name: 'campus_edit', methods: ['GET', 'POST'])]
    public function editCampus(Request $request, Campus $campus, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_campus_index');
        }

        return $this->render('admin/campus/edit.html.twig', [
            'campus' => $campus,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/campus/delete/{id}', name: 'campus_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteCampus(Campus $campus): Response
    {
        return $this->render('admin/campus/delete.html.twig', [
            'campus' => $campus,
        ]);
    }

    #[Route('/campus/confirm_delete/{id}', name: 'campus_confirm_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function confirmDeleteCampus(Request $request, Campus $campus, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $campus->getId(), $request->request->get('_token'))) {
            $entityManager->remove($campus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_campus_index');
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

            return $this->redirectToRoute('admin_dashboard');
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
        if ($this->isCsrfTokenValid('delete' . $city->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($city);
            $this->entityManager->flush();
            $this->addFlash('success', 'City deleted successfully.');
        }

        return $this->redirectToRoute('admin_city_list');
    }
}