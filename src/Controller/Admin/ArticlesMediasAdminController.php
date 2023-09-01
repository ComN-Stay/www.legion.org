<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FileUploaderService;
use App\Repository\ArticlesRepository;
use App\Repository\ArticlesMediasRepository;
use App\Form\ArticlesMediasType;
use App\Entity\ArticlesMedias;

#[Route('/admin/articlesMedias')]
class ArticlesMediasAdminController extends AbstractController
{

    #[Route('/new', name: 'app_articles_medias_admin_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        ArticlesRepository $articlesRepository, 
        FileUploaderService $fileUploader
    ): Response
    {
        $articlesMedia = new ArticlesMedias();
        $form = $this->createForm(ArticlesMediasType::class, $articlesMedia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $articlesRepository->find($request->request->get('fk_article'));
            $articlesMedia->setFkArticle($article);
            $file = $form['file']->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                if (null !== $fileName) {
                    $articlesMedia->setFile($fileName);
                }
            }
            
            $entityManager->persist($articlesMedia);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_articles_admin_edit', ['id' => $request->request->get('fk_article')], Response::HTTP_SEE_OTHER);
    }

    

    #[Route('/deleteLogo', name: 'app_articles_media_admin_delete_logo', methods: ['GET', 'POST'])]
    public function deleteLogo(
        Request $request, 
        ArticlesMediasRepository $articlesMediasRepository, 
        EntityManagerInterface $entityManager, 
        $kernelUploadDir
    ): JsonResponse
    {
        if ($request->isXMLHttpRequest()) {
            $res['result'] = 'error';
            $articleMedia = $articlesMediasRepository->find($request->request->get('id'));
            $file = $articleMedia->getFile();
            @unlink($kernelUploadDir . '/' . $file);
            $entityManager->remove($articleMedia);
            $res['result'] = 'success';
            $entityManager->flush();
            return new JsonResponse(json_encode($res));
        }
    
        return new Response('This is not ajax !', 400);
    }
}
