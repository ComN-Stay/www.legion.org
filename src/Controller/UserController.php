<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Consents;
use App\Service\MailService;
use App\Repository\UserRepository;
use App\Repository\PagesRepository;
use App\Repository\ConsentsRepository;
use App\Repository\PagesTypesRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        PagesTypesRepository $pagesTypesRepository,
        PagesRepository $pagesRepository,
        StatusRepository $statusRepository,
        MailService $mail
    ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $type = $form->get('type')->getData();
            $role = ($type == 'perso') ? 'ROLE_IDENTIFIED' : 'ROLE_ADMIN_CUSTOMER';
            $token = bin2hex(random_bytes(60));
            $user->setRoles([$role]);
            $hashPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashPassword);
            $user->setToken($token);
            $user->setIsCompanyAdmin(false);
            $entityManager->persist($user);
            $entityManager->flush();

            $status = $statusRepository->find(3);
            $pageType = $pagesTypesRepository->find(1);
            $consentType = $pagesRepository->findOneBy(['fk_type' => $pageType, 'fk_status' => $status]);
            $consents = new Consents;
            $consents->setFkUser($user);
            $consents->setFkType($consentType->getFkType());
            $consents->setDateAdd(new \DateTime(date('Y-m-d H:i:s')));
            $consents->setVersion($consentType->getVersion());
            $consents->setVersionDate($consentType->getdateAdd());
            $entityManager->persist($consents);
            $entityManager->flush();
            
            $rgpdConsent = $pagesTypesRepository->find(5);
            $rgpdFile = $pagesRepository->findOneBy(['fk_type' => $rgpdConsent, 'fk_status' => $status]);
            $consents = new Consents;
            $consents->setFkUser($user);
            $consents->setFkType($rgpdFile->getFkType());
            $consents->setDateAdd(new \DateTime(date('Y-m-d')));
            $consents->setVersion($rgpdFile->getVersion());
            $consents->setVersionDate($rgpdFile->getdateAdd());
            $entityManager->persist($consents);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été créé');
            if($type == 'perso') {
                $mail->sendMail([
                    'to' => $user->getEmail(),
                    'tpl' => 'welcome_user',
                    'vars' => [
                        'user' => $user,
                    ]
                ]);
                return $this->redirectToRoute('account_login', [], Response::HTTP_SEE_OTHER);
            } else {
                $typeId = ($type == 'asso') ? 1 : 2;
                return $this->redirectToRoute('app_company_new', ['typeId' => $typeId, 'userToken' => $token], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('front/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
