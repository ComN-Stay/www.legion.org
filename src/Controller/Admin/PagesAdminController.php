<?php

namespace App\Controller\Admin;

use App\Entity\Pages;
use App\Form\PagesType;
use App\Repository\PagesRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/pages')]
class PagesAdminController extends AbstractController
{
    #[Route('/list/{status?}', name: 'app_pages_admin_index', methods: ['GET'])]
    public function index(
        PagesRepository $pagesRepository,
        StatusRepository $statusRepository,
        $status
    ): Response
    {
        if($status !== null) {
            $status = $statusRepository->find($status);
        } else {
            $status = $statusRepository->findBy(['id' => [1, 2, 4, 5]]);
        }
        return $this->render('admin/pages_admin/index.html.twig', [
            'pages' => $pagesRepository->findBy(['fk_status' => $status]),
            'sidebar' => 'redac'
        ]);
    }

    #[Route('/new', name: 'app_pages_admin_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        PagesRepository $pagesRepository,
        StatusRepository $statusRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $sluggerInterface,
    ): Response
    {
        $page = new Pages();
        $form = $this->createForm(PagesType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($page->getFkType()->isHasVersion() == true && $page->getFkStatus()->getId() == 3) {
                $status = $statusRepository->find(3);
                $onlineDoc = $pagesRepository->findOneBy(['fk_type' => $page->getFkType(), 'fk_status' => $status]);dd($onlineDoc);
                $version = ($onlineDoc === null) ? 1 : $onlineDoc->getVersion() + 1;
                $page->setVersion($version);
                if($onlineDoc !== null) {
                    $oldStatus = $statusRepository->find(5);
                    $onlineDoc->setFkStatus($oldStatus);
                    $entityManager->persist($onlineDoc);
                }
            }
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
        PagesRepository $pagesRepository,
        StatusRepository $statusRepository
    ): Response
    {
        $form = $this->createForm(PagesType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uow = $entityManager->getUnitOfWork();
            $oldPage = $uow->getOriginalEntityData($page);
            if($page->getFkType()->isHasVersion() == true && $page->getFkStatus()->getId() == 3 && $oldPage['fk_status_id'] != 3) {
                $status = $statusRepository->find(3);
                $onlineDoc = $pagesRepository->findOneBy(['fk_type' => $page->getFkType(), 'fk_status' => $status]);
                $version = ($onlineDoc === null) ? 1 : $onlineDoc->getVersion() + 1;
                $page->setVersion($version);
                if($onlineDoc !== null) {
                    $oldStatus = $statusRepository->find(5);
                    $onlineDoc->setFkStatus($oldStatus);
                    $entityManager->persist($onlineDoc);
                }
            }
            $page->setSlug($sluggerInterface->slug($page->getTitle()));
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
