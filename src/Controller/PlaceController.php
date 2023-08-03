<?php

namespace App\Controller;

use App\Entity\Place;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/place', name: 'place_')]
class PlaceController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/list', name: 'list')]
    public function listPlace(PlaceRepository $placeRepository): Response
    {
        $places = $placeRepository->findAll();

        return $this->render('place/list.html.twig', [
            'places' => $places,
        ]);
    }

    #[Route('/add', name: 'add')]
    #[IsGranted('ROLE_ORGANISATEUR')]
    public function add(Request $request): Response
    {
        $place = new Place();
        $form = $this->createform(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($place);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le lieu a bien été ajouté');

            return $this->redirectToRoute('place_list');
        }

        return $this->render('place/add.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function detail(Place $place): Response
    {
        return $this->render('place/detail.html.twig', [
            'place' => $place,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    #[IsGranted('ROLE_ORGANISATEUR')]
    public function editPlace(Place $place, Request $request): Response
    {
        $form = $this->createForm(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Place updated successfully.');

            return $this->redirectToRoute('place_list');
        }

        return $this->render('place/edit.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    #[IsGranted('ROLE_ORGANISATEUR')]
    public function deletePlace(Place $place): Response
    {
        return $this->render('place/delete.html.twig', [
            'place' => $place
        ]);
    }

    #[Route('/confirm-delete/{id}', name: 'confirm_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ORGANISATEUR')]
    public function confirmDeletePlace(Request $request, Place $place): Response
    {
        if ($this->isCsrfTokenValid('delete' . $place->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($place);
            $this->entityManager->flush();
            $this->addFlash('success', 'Place deleted successfully.');
        }

        return $this->redirectToRoute('place_list');
    }
}
