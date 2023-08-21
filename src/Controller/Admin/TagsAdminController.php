<?php

namespace App\Controller\Admin;

use App\Entity\Tags;
use App\Form\TagsType;
use App\Repository\TagsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/tags')]
class TagsAdminController extends AbstractController
{
    #[Route('/', name: 'app_tags_admin_index', methods: ['GET'])]
    public function index(TagsRepository $tagsRepository): Response
    {
        return $this->render('admin/tags_admin/index.html.twig', [
            'tags' => $tagsRepository->findAll(),
            'sidebar' => 'redac'
        ]);
    }

    #[Route('/new', name: 'app_tags_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $tag = new Tags();
        $form = $this->createForm(TagsType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag->setArticleQte(0);
            $tag->setSlug($slugger->slug($tag->getTitle()));
            if($tag->getMetaName() == '') {
                $tag->setMetaName($tag->getTitle());
            }
            $entityManager->persist($tag);
            $entityManager->flush();
            $this->addFlash('success', 'Tag ajouté');

            return $this->redirectToRoute('app_tags_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/tags_admin/new.html.twig', [
            'tag' => $tag,
            'form' => $form,
            'sidebar' => 'redac'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tags_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tags $tag, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(TagsType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag->setSlug($slugger->slug($tag->getTitle()));
            if($tag->getMetaName() == '') {
                $tag->setMetaName($tag->getTitle());
            }
            $entityManager->flush();
            $this->addFlash('success', 'Tag modifié');

            return $this->redirectToRoute('app_tags_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/tags_admin/edit.html.twig', [
            'tag' => $tag,
            'form' => $form,
            'sidebar' => 'redac'
        ]);
    }

    #[Route('/{id}', name: 'app_tags_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Tags $tag, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tag);
            $entityManager->flush();
            $this->addFlash('success', 'Tag supprimé');
        }

        return $this->redirectToRoute('app_tags_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
