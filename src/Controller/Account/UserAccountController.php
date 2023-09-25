<?php

namespace App\Controller\Account;

use App\Entity\User;
use App\Form\UserType;
use App\Service\MailService;
use App\Repository\UserRepository;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/account/user')]
class UserAccountController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(
        UserRepository $userRepository
    ): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        FileUploaderService $fileUploader,
        MailService $mail
    ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $type = $form->get('type')->getData();
            $role = ($type == 'perso') ? 'ROLE_IDENTIFIED' : 'ROLE_ADMIN_CUSTOMER';
            if($role == 'ROLE_IDENTIFIED') {
                $user->setAdvertsAuth(false);
                $user->setArticlesAuth(true);
                $user->setPetitionsAuth(true);
                $user->setBoAccessAuth(false);
            } elseif($role == 'ROLE_ADMIN_CUSTOMER') {
                $user->setAdvertsAuth(true);
                $user->setArticlesAuth(true);
                $user->setPetitionsAuth(true);
                $user->setBoAccessAuth(true);
            }
            $file = $form['picture']->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                if (null !== $fileName) {
                    $user->setPicture($fileName);
                }
            }
            $token = bin2hex(random_bytes(60));
            $user->setRoles([$role]);
            $hashPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashPassword);
            $user->setToken($token);
            $user->setIsCompanyAdmin(false);
            $entityManager->persist($user);
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

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(
        User $user
    ): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        User $user, 
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
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
