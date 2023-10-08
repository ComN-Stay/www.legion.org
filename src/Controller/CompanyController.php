<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MailService;
use App\Service\FileUploaderService;
use App\Service\CallGoogleApiService;
use App\Repository\UserRepository;
use App\Repository\CompanyTypeRepository;
use App\Repository\CompanyRepository;
use App\Form\CompanyType;
use App\Entity\Company;

#[Route('/company')]
class CompanyController extends AbstractController
{
    #[Route('/list/{type}', name: 'app_company_index', methods: ['GET'])]
    public function index(
        CompanyRepository $companyRepository, 
        CompanyTypeRepository $companyTypeRepository, 
        $type
    ): Response
    {
        $idType = ($type == 'associations') ? 1 : 2;
        $type = $companyTypeRepository->find($idType);
        return $this->render('front/company/index.html.twig', [
            'companies' => $companyRepository->findBy(['fk_company_type' => $type, 'status' => 1]),
        ]);
    }

    #[Route('/new/{typeId}/{userToken}', name: 'app_company_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        CompanyTypeRepository $companyTypeRepository, 
        EntityManagerInterface $entityManager, 
        FileUploaderService $fileUploader,
        UserRepository $userRepository,
        MailService $mail,
        $typeId, $userToken
    ): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $type = $companyTypeRepository->find($form->get('type_id')->getData());
            $company->setstatus(false);
            $company->setFkCompanyType($type);
            $file = $form['logo']->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                if (null !== $fileName) {
                    $company->setLogo($fileName);
                }
            }
            
            $entityManager->persist($company);
            $entityManager->flush();

            $user = $userRepository->findOneBy(['token' => $form->get('userToken')->getData()]);
            $user->setFkCompany($company);
            $user->setIsCompanyAdmin(true);
            $entityManager->persist($user);
            $entityManager->flush();

            $mail->sendMail([
                'to' => $user->getEmail(),
                'tpl' => 'welcome_company',
                'vars' => [
                    'user' => $user,
                    'company' => $company
                ]
            ]);

            $this->addFlash('success', 'Le compte de votre structure a été créé, vous recevrez un email lorsque celui ci aura été validé par un administrateur');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/company/new.html.twig', [
            'company' => $company,
            'form' => $form,
            'typeId' => $typeId,
            'userToken' => $userToken
        ]);
    }

    #[Route('/{id}', name: 'app_company_show', methods: ['GET'])]
    public function show(
        Company $company, 
        UserRepository $userRepository,
        $googleApiKey
    ): Response
    {
        return $this->render('front/company/show.html.twig', [
            'company' => $company,
            'googleKey' => $googleApiKey,
            'users' => $userRepository->findBy(['fk_company' => $company])
        ]);
    }
}
