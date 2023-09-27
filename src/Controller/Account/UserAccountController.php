<?php

namespace App\Controller\Account;

use App\Entity\User;
use App\Form\UserType;
use App\Service\MailService;
use App\Repository\UserRepository;
use App\Service\FileUploaderService;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/account/user')]
class UserAccountController extends AbstractController
{
    #[Route('/', name: 'app_user_account_index', methods: ['GET'])]
    public function index(
        UserRepository $userRepository,
        Security $security,
    ): Response
    {
        return $this->render('account/user/index.html.twig', [
            'users' => $userRepository->findBy(['fk_company' => $security->getUser()->getFkCompany()]),
        ]);
    }

    #[Route('/new', name: 'app_user_account_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        ResetPasswordHelperInterface $resetPasswordHelper, 
        CompanyRepository $companyRepository,
        FileUploaderService $fileUploader,
        MailService $mail
    ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $company = $companyRepository->find($user->getFkCompany()->getId());
            $type = $form->get('type')->getData();
            $file = $form['picture']->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                if (null !== $fileName) {
                    $user->setPicture($fileName);
                }
            }
            $token = bin2hex(random_bytes(60));
            $user->setToken($token);
            $user->setIsCompanyAdmin(false);
            $user->setFkCompany($company);
            $entityManager->persist($user);
            $entityManager->flush();
            $resetToken = $resetPasswordHelper->generateResetToken($user);
            $mail->sendMail([
                'to' => $user->getEmail(),
                'tpl' => 'company_team_user_create',
                'vars' => [
                    'user' => $user,
                    'resetToken' => $resetToken,
                    'company' => $company
                    ]
                ]);
            $this->addFlash('success', 'Compte créé avec succès');
            return $this->redirectToRoute('app_user_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_account_show', methods: ['GET'])]
    public function show(
        User $user
    ): Response
    {
        return $this->render('account/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_account_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        User $user, 
        EntityManagerInterface $entityManager,
        FileUploaderService $fileUploader,
        CompanyRepository $companyRepository,
        Security $security,
        $kernelUploadDir
    ): Response
    {
        $company = $companyRepository->find($user->getFkCompany()->getId());
        if(($security->getUser() != $user && $security->getUser()->getRoles()[0] != 'ROLE_ADMIN_CUSTOMER') || $security->getUser()->getFkCompany()->getId() != $company->getId()) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette ressource');
            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uow = $entityManager->getUnitOfWork();
            $oldValues = $uow->getOriginalEntityData($user);
            $file = $form['picture']->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                if (null !== $fileName) {
                    $user->setPicture($fileName);
                }
                unlink($kernelUploadDir . '/' . $oldValues['picture']);
            }
            if($user->getPassword() == null) {
                $user->setPassword($oldValues['password']);
            }
            
            $entityManager->flush();

            $this->addFlash('success', 'Compte mis à jour');

            return $this->redirectToRoute('app_user_account_edit', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        User $user, 
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
