<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\PlaceRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
#[IsGranted('ROLE_USER')]
class OutingController extends AbstractController
{
    #[Route('/', name: 'outing_list')]
    public function list(OutingRepository $sortieRepository): Response
    {

        $outings = $sortieRepository->findBy([],[ 'dateHeureDebut' => 'DESC']);

        return $this->render('outing/outing.html.twig', [
            "outings" => $outings
        ]);
    }

    #[Route('/create', name: 'outing_create')]
    public function create(Request $request, PlaceRepository $placeRepository, StatusRepository $statusRepository, EntityManagerInterface $entityManager): Response
    {
        $outing = new Outing();
        $outingForm = $this->createForm(OutingType::class, $outing);

        $outingForm->handleRequest($request);
        if ($outingForm->isSubmitted() && $outingForm->isValid()) {
            // Set the campus to the user's campus
            $outing->setCampus($this->getUser()->getCampus());

            // Set the status to a default status
            $defaultStatus = $statusRepository->find(1); // Replace 1 with the id of the status you want to set as default
            $outing->setStatus($defaultStatus);

            // Set the organizer to the current user
            $outing->setOrganizer($this->getUser());

            // Persist and flush the new Outing entity
            $entityManager->persist($outing);
            $entityManager->flush();

            // Add a flash message
            $this->addFlash('success', 'La sortie a été créée avec succès !');

            // Redirect to the outing list page
            return $this->redirectToRoute('outing_list');
        }

        $places = $placeRepository->findAll();

        return $this->render('outing/create.html.twig', [
            "outingForm" => $outingForm->createView(),
            "places" => $places
        ]);
    }

}
