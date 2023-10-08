<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Service\MailService;
use App\Repository\UserRepository;
use App\Repository\ArticlesRepository;
use App\Repository\ConsentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/admin/user')]
class UserAdminController extends AbstractController
{
    #[Route('/', name: 'app_user_admin_index', methods: ['GET'])]
    public function index(
        UserRepository $userRepository
    ): Response
    {
        return $this->render('admin/user_admin/index.html.twig', [
            'users' => $userRepository->findAll(),
            'sidebar' => 'users'
        ]);
    }

    #[Route('/new', name: 'app_user_admin_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        ResetPasswordHelperInterface $resetPasswordHelper,
        MailService $mail
    ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type = $form->get('type')->getData();
            $isAdmin = $form->get('is_admin')->getData();
            if($form->get('bo_access_auth')->getData()) {
                if($form->get('is_admin')->getData()) {
                    $user->setRoles(['ROLE_ADMIN_CUSTOMER']);
                } else {
                    $user->setRoles(['ROLE_CUSTOMER']);
                }
            } else {
                $user->setRoles(['ROLE_IDENTIFIED']);
            }
            $token = bin2hex(random_bytes(60));
            $user->setToken($token);
            $user->setIsCompanyAdmin($isAdmin);
            $entityManager->persist($user);
            $entityManager->flush();
            $resetToken = $resetPasswordHelper->generateResetToken($user);
            $mail->sendMail([
                'to' => $user->getEmail(),
                'tpl' => 'user_password_create',
                'vars' => [
                    'user' => $user,
                    'resetToken' => $resetToken,
                ]
            ]);

            return $this->redirectToRoute('app_user_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user_admin/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'sidebar' => 'users'
        ]);
    }

    #[Route('/{id}', name: 'app_user_admin_show', methods: ['GET'])]
    public function show(
        User $user,
        ArticlesRepository $articlesRepository,
        ConsentsRepository $consentsRepository
    ): Response
    {
        return $this->render('admin/user_admin/show.html.twig', [
            'user' => $user,
            'sidebar' => 'users',
            'articles' => $articlesRepository->findBy(['fk_user' => $user]),
            'consents' => $consentsRepository->findBy(['fk_user' => $user])
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_admin_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        User $user, 
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uow = $entityManager->getUnitOfWork();
            $oldValues = $uow->getOriginalEntityData($user);
            $user->setPassword($oldValues['password']);
            $user->setPicture($oldValues['picture']);
            $user->setToken($oldValues['token']);
            if($form->get('bo_access_auth')->getData()) {
                if($form->get('is_admin')->getData()) {
                    $user->setRoles(['ROLE_ADMIN_CUSTOMER']);
                } else {
                    $user->setRoles(['ROLE_CUSTOMER']);
                }
            } else {
                $user->setRoles(['ROLE_IDENTIFIED']);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_user_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user_admin/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'sidebar' => 'users'
        ]);
    }

    #[Route('/{id}', name: 'app_user_admin_delete', methods: ['POST'])]
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

        return $this->redirectToRoute('app_user_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
