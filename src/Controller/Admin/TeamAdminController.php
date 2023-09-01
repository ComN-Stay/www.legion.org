<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TeamRepository;
use App\Form\TeamType;
use App\Entity\Team;
use App\Service\MailService;

#[Route('/admin/team')]
class TeamAdminController extends AbstractController
{
    #[Route('/', name: 'app_team_index', methods: ['GET'])]
    public function index(
        TeamRepository $teamRepository
    ): Response
    {
        return $this->render('admin/team/index.html.twig', [
            'teams' => $teamRepository->findAll(),
            'sidebar' => 'users'
        ]);
    }

    #[Route('/new', name: 'app_team_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        ResetPasswordHelperInterface $resetPasswordHelper,
        MailService $mail
    ): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plaintextPassword = $team->getPassword();
            if($plaintextPassword != '') {
                $hashedPassword = $passwordHasher->hashPassword($team, $plaintextPassword);
                $team->setPassword($hashedPassword);
            } 
            $entityManager->persist($team);
            $entityManager->flush();
            if($plaintextPassword == '')  {
                $resetToken = $resetPasswordHelper->generateResetToken($team);
                $mail->sendMail([
                    'to' => $team->getEmail(),
                    'tpl' => 'admin_password_create',
                    'vars' => [
                        'team' => $team,
                        'resetToken' => $resetToken,
                    ]
                ]);
            }

            $this->addFlash('success', 'Création effectuée');

            return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/team/new.html.twig', [
            'team' => $team,
            'form' => $form,
            'sidebar' => 'users'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_team_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Team $team, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plaintextPassword = $team->getPassword();
            if($plaintextPassword != '') {
                $hashedPassword = $passwordHasher->hashPassword($team, $plaintextPassword);
            }
            $team->setPassword($hashedPassword);
            $entityManager->flush();
            $this->addFlash('success', 'Modification effectuée');

            return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/team/edit.html.twig', [
            'team' => $team,
            'form' => $form,
            'sidebar' => 'users'
        ]);
    }

    #[Route('/{id}', name: 'app_team_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Team $team, 
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $entityManager->remove($team);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectuée');
        }

        return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
    }
}
