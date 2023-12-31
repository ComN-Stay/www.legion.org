<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use App\Form\ArticlesType;
use App\Entity\ArticlesMedias;
use App\Form\ArticlesMediasType;
use App\Repository\StatusRepository;
use App\Service\FileUploaderService;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\ArticlesMediasRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/articles')]
class ArticlesAdminController extends AbstractController
{
    #[Route('/list/{status?}', name: 'app_articles_admin_index', methods: ['GET'])]
    public function index(
        ArticlesRepository $articlesRepository, 
        StatusRepository $statusRepository,
        Security $security,
        $status
    ): Response
    {
        $stat = $statusRepository->find(2);
        $stat = $statusRepository->find(3);
        $redac = $statusRepository->find(1);
        if($status == 1) {
            $articles = $articlesRepository->findBy(['fk_status' => $redac, 'fk_team' => $security->getUser()]);
        } else {
            $articles = $articlesRepository->findBy(['fk_status' => $stat]);
        }
        return $this->render('admin/articles_admin/index.html.twig', [
            'articles' => $articles,
            'sidebar' => 'redac'
        ]);
    }

    #[Route('/new', name: 'app_articles_admin_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        FileUploaderService $fileUploader, 
        SluggerInterface $sluggerInterface,
        StatusRepository $statusRepository,
        Security $security,
    ): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($sluggerInterface->slug($article->getTitle()));
            $article->setFkStatus($statusRepository->find(1));
            $article->setFkTeam($security->getUser());
            $article->setDateAdd(new \DateTime(date('Y-m-d')));
            $article->setVisits(0);
            $file = $form['logo']->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                if (null !== $fileName) {
                    $article->setLogo($fileName);
                }
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_articles_admin_edit', ['id' => $article->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/articles_admin/new.html.twig', [
            'article' => $article,
            'form' => $form,
            'sidebar' => 'redac',
            'formMedia' => null
        ]);
    }

    #[Route('/activation', name: 'app_articles_admin_activation', methods: ['GET', 'POST'])]
    public function activation(
        Request $request, 
        ArticlesRepository $articlesRepository, 
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        if ($request->isXMLHttpRequest()) {
            $res['result'] = 'error';
            $article = $articlesRepository->find($request->request->get('id'));
            $article->setFkStatus($request->request->get('status'));
            $entityManager->persist($article);
            $res['result'] = 'success';
            $entityManager->flush();
            return new JsonResponse(json_encode($res));
        }
    
        return new Response('This is not ajax !', 400);
    }

    #[Route('/deleteLogo', name: 'app_articles_admin_delete_logo', methods: ['GET', 'POST'])]
    public function deleteLogo(
        Request $request, 
        ArticlesRepository $articlesRepository, 
        EntityManagerInterface $entityManager, 
        $kernelUploadDir
    ): JsonResponse
    {
        if ($request->isXMLHttpRequest()) {
            $res['result'] = 'error';
            $article = $articlesRepository->find($request->request->get('id'));
            $logo = $article->getLogo();
            @unlink($kernelUploadDir . '/' . $logo);
            $article->setLogo(null);
            $entityManager->persist($article);
            $res['result'] = 'success';
            $entityManager->flush();
            return new JsonResponse(json_encode($res));
        }
    
        return new Response('This is not ajax !', 400);
    }

    #[Route('/{id}', name: 'app_articles_admin_show', methods: ['GET'])]
    public function show(Articles $article): Response
    {
        return $this->render('admin/articles_admin/show.html.twig', [
            'article' => $article,
            'tags' => $article->getTags()->toArray(),
            'medias' => $article->getArticleMedias()->toArray(),
            'sidebar' => 'redac'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_articles_admin_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Articles $article, 
        ArticlesMedias $articlesMedias, 
        EntityManagerInterface $entityManager, 
        FileUploaderService $fileUploader, 
        SluggerInterface $sluggerInterface,
        $kernelUploadDir
    ): Response
    {
        $form = $this->createForm(ArticlesType::class, $article);
        $mediaForm = $this->createForm(ArticlesMediasType::class, $articlesMedias);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($sluggerInterface->slug($article->getTitle()));
            $file = $form['logo']->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                if (null !== $fileName) {
                    $article->setLogo($fileName);
                    $uow = $entityManager->getUnitOfWork();
                    $oldValues = $uow->getOriginalEntityData($article);
                    @unlink($kernelUploadDir . '/' . $oldValues['logo']);
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_articles_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/articles_admin/edit.html.twig', [
            'article' => $article,
            'form' => $form,
            'medias' => $article->getArticleMedias()->toArray(),
            'formMedia' => $mediaForm,
            'sidebar' => 'redac'
        ]);
    }

    #[Route('/{id}', name: 'app_articles_admin_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Articles $article, 
        EntityManagerInterface $entityManager, 
        ArticlesMediasRepository $articlesMediasRepository, 
        $kernelUploadDir
    ): Response
    {
        $medias = $articlesMediasRepository->findBy(['fk_article' => $article->getId()]);
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $logo = $article->getLogo();
            $entityManager->remove($article);
            $entityManager->flush();
            @unlink($kernelUploadDir . '/' . $logo);
            foreach($medias as $media) {
                @unlink($kernelUploadDir . '/' . $media->getFile());
                $entityManager->remove($media);
            }
            $this->addFlash('success', 'Article Supprimé');
        }

        return $this->redirectToRoute('app_articles_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
