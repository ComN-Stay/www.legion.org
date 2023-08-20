<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FileUploaderService;
use App\Repository\TagsRepository;
use App\Repository\ArticlesRepository;
use App\Form\ArticlesType;
use App\Entity\Articles;

#[Route('/admin/articles')]
class ArticlesAdminController extends AbstractController
{
    #[Route('/list/{status?}', name: 'app_articles_admin_index', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository, $status): Response
    {
        if($status === null) {
            $articles = $articlesRepository->findAll();
        } else {
            $articles = $articlesRepository->findBy(['status' => false]);
        }
        return $this->render('admin/articles_admin/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/new', name: 'app_articles_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploaderService $fileUploader): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['logo']->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                if (null !== $fileName) {
                    $fullPath = $fileName;
                }
                $article->setLogo($fullPath);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_articles_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/articles_admin/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/activation', name: 'app_adverts_admin_activation', methods: ['GET', 'POST'])]
    public function activation(Request $request, ArticlesRepository $articlesRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($request->isXMLHttpRequest()) {
            $res['result'] = 'error';
            $article = $articlesRepository->find($request->request->get('id'));
            $article->setStatus($request->request->get('status'));
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
            'tags' => $article->getTags()->toArray()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_articles_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, EntityManagerInterface $entityManager, FileUploaderService $fileUploader): Response
    {
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['logo']->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                if (null !== $fileName) {
                    $fullPath = $fileName;
                }
                $article->setLogo($fullPath);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_articles_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/articles_admin/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_articles_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $article, EntityManagerInterface $entityManager, $kernelUploadDir): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $logo = $article->getLogo();
            $entityManager->remove($article);
            $entityManager->flush();
            @unlink($kernelUploadDir . '/' . $logo);
        }

        return $this->redirectToRoute('app_articles_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
