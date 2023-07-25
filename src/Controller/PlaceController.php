<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaceController extends AbstractController
{
    #[Route('/place', name: 'app_place')]
    public function index(): Response
    {
        return $this->render('place/place.html.twig', [
            'controller_name' => 'PlaceController',
        ]);
    }
}
