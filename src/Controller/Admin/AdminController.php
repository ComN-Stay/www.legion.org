<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\CompanyRepository;
use App\Repository\AdvertsRepository;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(
        CompanyRepository $companyRepository,
        AdvertsRepository $advertsRepository,
    ): Response
    {
        $boxes = [];
        $associations = count($companyRepository->findBy(['status' => 0, 'fk_company_type' => 1]));
        if($associations > 0) {
            $boxes['associations'] = $associations;
        }
        $eleveurs = count($companyRepository->findBy(['status' => 0, 'fk_company_type' => 2]));
        if($eleveurs > 0) {
            $boxes['eleveurs'] = $eleveurs;
        }
        $adverts = count($advertsRepository->findBy(['status' => 0]));
        if($adverts > 0) {
            $boxes['adverts'] = $adverts;
        }
        return $this->render('admin/dashboard.html.twig', [
            'boxes' => $boxes,
            'adverts' => count($advertsRepository->findBy(['status' => 1])),
            'associations' => count($companyRepository->findBy(['status' => 1, 'fk_company_type' => 1])),
            'eleveurs' => count($companyRepository->findBy(['status' => 1, 'fk_company_type' => 2])),
            'today' => date('Y-m-d'),
            'lastMounth' => date('Y-m-d', strtotime('-1 month')),
            'week' => date("Y-m-d", strtotime('monday this week')),
            'month' => date('01-m-Y'),
            'year' => date('01-01-Y')
        ]);
    }
}
