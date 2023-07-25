<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    #[Route('/city', name: 'app_city')]
    public function index(): Response
    {
        return $this->render('city/city.html.twig', [
            'controller_name' => 'CityController',
        ]);
    }
}
