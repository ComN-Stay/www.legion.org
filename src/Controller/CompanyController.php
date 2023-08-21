<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CompanyTypeRepository;
use App\Repository\CompanyRepository;
use App\Form\Company1Type;
use App\Entity\Company;

#[Route('/company')]
class CompanyController extends AbstractController
{
    #[Route('/list/{type}', name: 'app_company_index', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository, CompanyTypeRepository $companyTypeRepository, $type): Response
    {
        $idType = ($type == 'associations') ? 1 : 2;
        $type = $companyTypeRepository->find($idType);
        return $this->render('front/company/index.html.twig', [
            'companies' => $companyRepository->findBy(['fk_company_type' => $type]),
        ]);
    }

    #[Route('/new', name: 'app_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $company = new Company();
        $form = $this->createForm(Company1Type::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($company);
            $entityManager->flush();

            return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company/new.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_company_show', methods: ['GET'])]
    public function show(Company $company, $googleApiKey): Response
    {
        return $this->render('front/company/show.html.twig', [
            'company' => $company,
            'googleKey' => $googleApiKey
        ]);
    }
}
