<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FileUploaderService;
use App\Service\CallGoogleApiService;
use App\Repository\CompanyTypeRepository;
use App\Repository\CompanyRepository;
use App\Form\CompanyType;
use App\Entity\Company;

#[Route('/admin/company')]
class CompanyAdminController extends AbstractController
{
    #[Route('/list/{idType}', name: 'app_company_admin_index', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository, CompanyTypeRepository $companyTypeRepository, $idType): Response
    {
        $type = $companyTypeRepository->find($idType);
        return $this->render('admin/company_admin/index.html.twig', [
            'company_type' => $type->getName(),
            'idType' => $idType,
            'companies' => $companyRepository->findBy(['fk_company_type' => $type]),
        ]);
    }

    #[Route('/new/{idType}', name: 'app_company_admin_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        CompanyTypeRepository $companyTypeRepository, 
        FileUploaderService $file_uploader, 
        CallGoogleApiService $callGoogleApiService,
        $idType, $publicUploadDir
    ): Response
    {
        $companyType = $companyTypeRepository->find($idType);
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['logo']->getData();
            if ($file) {
                $file_name = $file_uploader->upload($file);
                if (null !== $file_name) {
                    $full_path = $publicUploadDir.'/'.$file_name;
                }
                $company->setLogo($full_path);
            }
            $address = urlencode($company->getAddress() . ' ' . $company->getZipCode() . ' ' . $company->getTown());
            $geolocalization = $callGoogleApiService->getGeolocalization($address);
            if($geolocalization['status'] == 'OK') {
                $company->setLatitude($geolocalization['results'][0]['geometry']['location']['lat']);
                $company->setLongitude($geolocalization['results'][0]['geometry']['location']['lng']);
            }
            $company->setFkCompanyType($companyType);
            $entityManager->persist($company);
            $entityManager->flush();

            return $this->redirectToRoute('app_company_admin_index', ['idType' => $idType], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/company_admin/new.html.twig', [
            'company' => $company,
            'idType' => $idType,
            'typeName' => $companyType->getName(),
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_company_admin_show', methods: ['GET'])]
    public function show(Company $company): Response
    {
        return $this->render('admin/company_admin/show.html.twig', [
            'idType' => $company->getFkCompanyType()->getId(),
            'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_company_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, EntityManagerInterface $entityManager, FileUploaderService $file_uploader, CallGoogleApiService $callGoogleApiService, $publicUploadDir): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['logo']->getData();
            if ($file) {
                $file_name = $file_uploader->upload($file);
                if (null !== $file_name) {
                    $full_path = $publicUploadDir.'/'.$file_name;
                }
                $company->setLogo($full_path);
            }
            if($company->getLatitude() == '' || $company->getLatitude() == null) {
                $address = urlencode($company->getAddress() . ' ' . $company->getZipCode() . ' ' . $company->getTown());
                $geolocalization = $callGoogleApiService->getGeolocalization($address);
                if($geolocalization['status'] == 'OK') {
                    $company->setLatitude($geolocalization['results'][0]['geometry']['location']['lat']);
                    $company->setLongitude($geolocalization['results'][0]['geometry']['location']['lng']);
                }
            }
            $entityManager->persist($company);
            $entityManager->flush();

            $idType = $company->getFkCompanyType()->getId();
            return $this->redirectToRoute('app_company_admin_index', ['idType' => $idType], Response::HTTP_SEE_OTHER);
        }
       
        return $this->render('admin/company_admin/edit.html.twig', [
            'company' => $company,
            'form' => $form,
            'idType' => $company->getFkCompanyType()->getId()
        ]);
    }

    #[Route('/{id}', name: 'app_company_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        $idType = $company->getFkCompanyType()->getId();

        if ($this->isCsrfTokenValid('delete'.$company->getId(), $request->request->get('_token'))) {
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_company_admin_index', ['idType' => $idType], Response::HTTP_SEE_OTHER);
    }
}
