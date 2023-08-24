<?php

namespace App\Controller\Account;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FileUploaderService;
use App\Service\CallGoogleApiService;
use App\Repository\UserRepository;
use App\Repository\CompanyTypeRepository;
use App\Repository\CompanyRepository;
use App\Form\CompanyType;
use App\Entity\Company;

#[Route('/account/company')]
class CompanyAccountController extends AbstractController
{

    #[Route('/edit', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        CompanyRepository $companyRepository, 
        EntityManagerInterface $entityManager,
        FileUploaderService $fileUploader, 
        CallGoogleApiService $callGoogleApiService,
        $kernelUploadDir
    ): Response
    {
        $user = $this->getUser();
        $company = $companyRepository->find($user->getFkCompany()->getId());
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['logo']->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                if (null !== $fileName) {
                    $company->setLogo($fileName);
                    $uow = $entityManager->getUnitOfWork();
                    $oldValues = $uow->getOriginalEntityData($company);
                    @unlink($kernelUploadDir . '/' . $oldValues['logo']);
                }
            }
            $address = urlencode($company->getAddress() . ' ' . $company->getZipCode() . ' ' . $company->getTown());
            $geolocalization = $callGoogleApiService->getGeolocalization($address);
            if($geolocalization['status'] == 'OK') {
                $company->setLatitude($geolocalization['results'][0]['geometry']['location']['lat']);
                $company->setLongitude($geolocalization['results'][0]['geometry']['location']['lng']);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Modifications effectuées !');
            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/company/edit.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

}
