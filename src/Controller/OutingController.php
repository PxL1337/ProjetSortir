<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Repository\OutingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class OutingController extends AbstractController
{
    #[Route('/list-sorties', name: 'app_outing')]
    public function list(OutingRepository $sortieRepository): Response
    {

        $outings = $sortieRepository->findBy([],[ 'dateHeureDebut' => 'DESC']);

        return $this->render('outing/outing.html.twig', [
            "outings" => $outings
        ]);
    }
}
