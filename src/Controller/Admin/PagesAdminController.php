<?php

namespace App\Controller\Admin;

use App\Entity\Pages;
use App\Form\PagesType;
use App\Repository\PagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/pages')]
class PagesAdminController extends AbstractController
{
    #[Route('/', name: 'app_pages_admin_index', methods: ['GET'])]
    public function index(
        PagesRepository $pagesRepository
    ): Response
    {
        return $this->render('admin/pages_admin/index.html.twig', [
            'pages' => $pagesRepository->findAll(),
            'sidebar' => 'redac'
        ]);
    }

    #[Route('/new', name: 'app_pages_admin_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        SluggerInterface $sluggerInterface,
    ): Response
    {
        $page = new Pages();
        $form = $this->createForm(PagesType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page->setSlug($sluggerInterface->slug($page->getTitle()));
            $page->setDateAdd(new \DateTime(date('Y-m-d')));
            $entityManager->persist($page);
            $entityManager->flush();

            return $this->redirectToRoute('app_pages_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/pages_admin/new.html.twig', [
            'page' => $page,
            'form' => $form,
            'sidebar' => 'redac'
        ]);
    }

    #[Route('/{id}', name: 'app_pages_admin_show', methods: ['GET'])]
    public function show(
        Pages $page
    ): Response
    {
        return $this->render('admin/pages_admin/show.html.twig', [
            'page' => $page,
            'sidebar' => 'redac'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pages_admin_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Pages $page, 
        EntityManagerInterface $entityManager,
        SluggerInterface $sluggerInterface,
    ): Response
    {
        $form = $this->createForm(PagesType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pages_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/pages_admin/edit.html.twig', [
            'page' => $page,
            'form' => $form,
            'sidebar' => 'redac'
        ]);
    }

    #[Route('/{id}', name: 'app_pages_admin_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Pages $page, 
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $entityManager->remove($page);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pages_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
