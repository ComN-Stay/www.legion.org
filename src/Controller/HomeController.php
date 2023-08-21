<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\FrontController;

class HomeController extends FrontController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('front/home/index.html.twig', [
            
        ]);
    }
}
