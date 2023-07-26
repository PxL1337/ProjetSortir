<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class OutingController extends AbstractController
{
    #[Route('/list-sorties', name: 'outing_list')]
    public function list(OutingRepository $sortieRepository): Response
    {

        $outings = $sortieRepository->findBy([],[ 'dateHeureDebut' => 'DESC']);

        return $this->render('outing/outing.html.twig', [
            "outings" => $outings
        ]);
    }

    #[Route('/create-sortie', name: 'outing_create')]
    public function create(Request $request): Response
    {

        $outing = new Outing();
        $outingForm = $this->createForm(OutingType::class, $outing);

        return $this->render('outing/create.html.twig', [
            "outingForm" => $outingForm->createView()
        ]);
    }

}
