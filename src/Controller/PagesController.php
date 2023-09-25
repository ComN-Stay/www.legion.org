<?php

namespace App\Controller;

use App\Entity\Pages;
use App\Repository\PagesRepository;
use App\Repository\PagesTypesRepository;
use App\Repository\StatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/page')]
class PagesController extends AbstractController
{
    
    #[Route('/{slug}', name: 'app_pages_show', methods: ['GET'])]
    public function show(
        Pages $page
    ): Response
    {
        return $this->render('front/pages/show.html.twig', [
            'page' => $page,
        ]);
    }

    public function getRoute(
        Request $request,
        PagesTypesRepository $pagesTypesRepository,
        PagesRepository $pagesRepository,
        StatusRepository $statusRepository
    ): Response
    {
        $typeId = $request->attributes->get('id');
        $status = $statusRepository->find(3);
        $type = $pagesTypesRepository->find($typeId);
        $page = $pagesRepository->findOneBy(['fk_type' => $type, 'fk_status' => $status]);
        
        return new Response(($page !== null) ? '/page/' . $page->getSlug() : null);
    }
}
