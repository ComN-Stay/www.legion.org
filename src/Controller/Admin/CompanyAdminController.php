<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FileUploaderService;
use App\Service\CallGoogleApiService;
use App\Repository\MediasRepository;
use App\Repository\CompanyTypeRepository;
use App\Repository\CompanyRepository;
use App\Repository\AdvertsRepository;
use App\Form\CompanyType;
use App\Entity\Company;

#[Route('/admin/company')]
class CompanyAdminController extends AbstractController
{
    #[Route('/list/{idType}/{status?}', name: 'app_company_admin_index', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository, CompanyTypeRepository $companyTypeRepository, $idType, $status): Response
    {
        $type = $companyTypeRepository->find($idType);
        $conditions = ['fk_company_type' => $type];
        if($status !== null) {
            $conditions['status'] = 0;
        }
        return $this->render('admin/company_admin/index.html.twig', [
            'company_type' => $type->getName(),
            'idType' => $idType,
            'companies' => $companyRepository->findBy($conditions),
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
                $fileName = $file_uploader->upload($file);
                if (null !== $fileName) {
                    $company->setLogo($fileName);
                }
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
            $this->addFlash('success', 'Création effectuée');

            return $this->redirectToRoute('app_company_admin_index', ['idType' => $idType], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/company_admin/new.html.twig', [
            'company' => $company,
            'idType' => $idType,
            'typeName' => $companyType->getName(),
            'form' => $form,
            'logoDir' => $publicUploadDir
        ]);
    }

    #[Route('/{id}', name: 'app_company_admin_show', methods: ['GET'])]
    public function show(Company $company, $publicUploadDir): Response
    {
        return $this->render('admin/company_admin/show.html.twig', [
            'idType' => $company->getFkCompanyType()->getId(),
            'company' => $company,
            'mediaFolder' => $publicUploadDir
        ]);
    }

    #[Route('/activation', name: 'app_company_admin_activation', methods: ['GET', 'POST'])]
    public function activation(Request $request, CompanyRepository $companyRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($request->isXMLHttpRequest()) {
            $res['result'] = 'error';
            $company = $companyRepository->find($request->request->get('id'));
            $company->setStatus($request->request->get('status'));
            $entityManager->persist($company);
            $res['result'] = 'success';
            $entityManager->flush();
            return new JsonResponse(json_encode($res));
        }
    
        return new Response('This is not ajax !', 400);
    }

    #[Route('/{id}/edit', name: 'app_company_admin_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Company $company, 
        EntityManagerInterface $entityManager, 
        FileUploaderService $fileUploader, 
        CallGoogleApiService $callGoogleApiService
        ): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['logo']->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                if (null !== $fileName) {
                    $company->setLogo($fileName);
                }
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
            $this->addFlash('success', 'Mise à jour effectuée');

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
    public function delete(
        Request $request, 
        Company $company, 
        EntityManagerInterface $entityManager, 
        MediasRepository $mediasRepository, 
        AdvertsRepository $advertsRepository, 
        $kernelUploadDir
        ): Response
    {
        $idType = $company->getFkCompanyType()->getId();
        $logo = $company->getLogo();
        $adverts = $advertsRepository->findBy(['fk_company' => $company->getId()]);
        if ($this->isCsrfTokenValid('delete'.$company->getId(), $request->request->get('_token'))) {
            $entityManager->remove($company);
            $entityManager->flush();
            @unlink($kernelUploadDir . '/' . $logo);
            foreach($adverts as $advert) {
                $medias = $mediasRepository->findBy(['fk_advert' => $advert->getId()]);
                foreach($medias as $media) {
                    @unlink($kernelUploadDir . '/' . $media->getFilename());
                }
            }
            $this->addFlash('success', 'Suppression effectuée');
        }

        return $this->redirectToRoute('app_company_admin_index', ['idType' => $idType], Response::HTTP_SEE_OTHER);
    }

}
