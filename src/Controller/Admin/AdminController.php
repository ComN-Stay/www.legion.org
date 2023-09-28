<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\CompanyRepository;
use App\Repository\AdvertsRepository;
use App\Repository\ArticlesRepository;
use App\Repository\PetitionsRepository;
use App\Repository\StatusRepository;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(
        CompanyRepository $companyRepository,
        AdvertsRepository $advertsRepository,
        PetitionsRepository $petitionsRepository,
        ArticlesRepository $articlesRepository,
        StatusRepository $statusRepository
    ): Response
    {
        $redac = $statusRepository->find(1);
        $pending = $statusRepository->find(2);
        $online = $statusRepository->find(3);
        $refused = $statusRepository->find(4);
        $boxes = [];
        $associations = count($companyRepository->findBy(['status' => false, 'fk_company_type' => 1]));
        $boxes['associations'] = ($associations > 0) ? $associations : null;
        
        $eleveurs = count($companyRepository->findBy(['status' => false, 'fk_company_type' => 2]));
        $boxes['eleveurs'] = ($eleveurs > 0) ? $eleveurs : null;
        
        $adverts = count($advertsRepository->findBy(['fk_status' => $online]));
        $boxes['adverts'] = ($adverts > 0) ? $adverts : null;
        
        $articles = count($articlesRepository->findBy(['fk_status' => $online]));
        $boxes['articles'] = ($articles > 0) ? $articles : null;
        
        $bestAdverts = $advertsRepository->findBy(['fk_status' => $online], ['visits' => 'DESC'], 5);
        $boxes['bestAdverts'] = ($bestAdverts > 0) ? $bestAdverts : null;
        
        $bestArticles = $articlesRepository->findBy(['fk_status' => $online], ['visits' => 'DESC'], 5);
        $boxes['bestArticles'] = ($bestArticles > 0) ? $bestArticles : null;
        
        $petitions = count($petitionsRepository->findBy(['status' => 0]));
        $boxes['petitions'] = ($petitions > 0) ? $petitions : null;
        
        return $this->render('admin/dashboard.html.twig', [
            'sidebar' => '',
            'boxes' => $boxes,
            'adverts' => count($advertsRepository->findBy(['fk_status' => $pending])),
            'articles' => count($articlesRepository->findBy(['fk_status' => $pending])),
            'associations' => count($companyRepository->findBy(['status' => true, 'fk_company_type' => 1])),
            'eleveurs' => count($companyRepository->findBy(['status' => 1, 'fk_company_type' => 2])),
            'petitions' => count($petitionsRepository->findBy(['status' => 1])),
            'today' => date('Y-m-d'),
            'lastMounth' => date('Y-m-d', strtotime('-1 month')),
            'week' => date("Y-m-d", strtotime('monday this week')),
            'month' => date('01-m-Y'),
            'year' => date('01-01-Y')
        ]);
    }
}
