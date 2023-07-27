<?php

namespace App\Controller;

use App\Entity\Place;
use App\Form\PlaceType;
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
    public function list(): Response
    {
        return $this->render('place/place.html.twig', [
            'controller_name' => 'PlaceController',
        ]);
    }

    #[Route('/add', name: 'add')]
    #[IsGranted('ROLE_PARTICIPANT')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $place = new Place();
        $form = $this->createform(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($place);
            $entityManager->flush();

            $this->addFlash('success', 'Le lieu a bien été ajouté');

            return $this->redirectToRoute('place_add');
        }

        return $this->render('place/add.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }
}
