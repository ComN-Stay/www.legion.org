<?php

namespace App\Controller\Admin;

use App\Entity\PagesTypes;
use App\Form\PagesTypesType;
use App\Repository\PagesTypesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/pages_type')]
class PagesTypesAdminController extends AbstractController
{
    #[Route('/', name: 'app_pages_type_admin_index', methods: ['GET'])]
    public function index(
        PagesTypesRepository $pagesTypesRepository
    ): Response
    {
        return $this->render('admin/pages_type_admin/index.html.twig', [
            'pages_types' => $pagesTypesRepository->findAll(),
            'sidebar' => 'params'
        ]);
    }

    #[Route('/new', name: 'app_pages_type_admin_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager
    ): Response
    {
        $pagesType = new PagesTypes();
        $form = $this->createForm(PagesTypesType::class, $pagesType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pagesType);
            $entityManager->flush();

            return $this->redirectToRoute('app_pages_type_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/pages_type_admin/new.html.twig', [
            'pages_type' => $pagesType,
            'form' => $form,
            'sidebar' => 'params'
        ]);
    }

    #[Route('/{id}', name: 'app_pages_type_admin_show', methods: ['GET'])]
    public function show(
        PagesTypes $pagesType
    ): Response
    {
        return $this->render('admin/pages_type_admin/show.html.twig', [
            'pages_type' => $pagesType,
            'sidebar' => 'params'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pages_type_admin_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        PagesTypes $pagesType, 
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(PagesTypesType::class, $pagesType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pages_type_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/pages_type_admin/edit.html.twig', [
            'pages_type' => $pagesType,
            'form' => $form,
            'sidebar' => 'params'
        ]);
    }

    #[Route('/{id}', name: 'app_pages_type_admin_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        PagesTypes $pagesType, 
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pagesType->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pagesType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pages_type_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
