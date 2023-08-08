<?php

namespace App\Controller\Admin;

use App\Entity\CompanyType;
use App\Form\CompanyTypeType;
use App\Repository\CompanyTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/company_type')]
class CompanyTypeAdminController extends AbstractController
{
    #[Route('/', name: 'app_company_type_admin_index', methods: ['GET'])]
    public function index(CompanyTypeRepository $companyTypeRepository): Response
    {
        return $this->render('admin/company_type_admin/index.html.twig', [
            'company_types' => $companyTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_company_type_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $companyType = new CompanyType();
        $form = $this->createForm(CompanyTypeType::class, $companyType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($companyType);
            $entityManager->flush();
            $this->addFlash('success', 'Création effectuée');

            return $this->redirectToRoute('app_company_type_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/company_type_admin/new.html.twig', [
            'company_type' => $companyType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_company_type_admin_show', methods: ['GET'])]
    public function show(CompanyType $companyType): Response
    {
        return $this->render('admin/company_type_admin/show.html.twig', [
            'company_type' => $companyType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_company_type_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CompanyType $companyType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CompanyTypeType::class, $companyType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Modification effectuée');

            return $this->redirectToRoute('app_company_type_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/company_type_admin/edit.html.twig', [
            'company_type' => $companyType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_company_type_admin_delete', methods: ['POST'])]
    public function delete(Request $request, CompanyType $companyType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$companyType->getId(), $request->request->get('_token'))) {
            $entityManager->remove($companyType);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectuée');
        }

        return $this->redirectToRoute('app_company_type_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
