<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/Bisous', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/main.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/Snake', name: 'app_snake')]
    public function snake(): Response
    {
        return $this->render('main/snake.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
