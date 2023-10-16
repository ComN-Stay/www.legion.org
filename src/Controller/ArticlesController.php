<?php

namespace App\Controller;

use App\Entity\Articles;
use Pagerfanta\Pagerfanta;
use App\Form\Articles1Type;
use App\Repository\TagsRepository;
use App\Repository\StatusRepository;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/articles')]
class ArticlesController extends AbstractController
{
    #[Route('/{tagSlug?}', name: 'app_articles_index', methods: ['GET', 'POST'])]
    public function index(
        ArticlesRepository $articlesRepository,
        TagsRepository $tagsRepository,
        StatusRepository $statusRepository,
        Request $request,
        $tagSlug
        ): Response
    {
        $session = $request->getSession();
        $status = $statusRepository->find(3);
        $tag = ($tagSlug !== null) ? $tagsRepository->findOneBy(['slug' => $tagSlug]) : null;
        $order_by = 'date_add';
        $order_way = 'DESC';
        if($session->get('order') == null) {
            $session->set('order', 'date_add-DESC');
        }
        
        if($request->getMethod() == 'POST') {
            $session->set('order', $request->request->get('order'));
            $exp = explode('-', $request->request->get('order'));
            $order_by = $exp[0];
            $order_way = $exp[1];
        }
        $queryBuilder = $articlesRepository->createOrderedByDateQueryBuilder($status, $tag, $order_by, $order_way);
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('page', 1),
            10
        );
        
        return $this->render('front/articles/index.html.twig', [
            'articles' => $pagerfanta,
            'tags' => $tagsRepository->findAll()
        ]);
    }

    #[Route('/{id}', name: 'app_articles_show', methods: ['GET'])]
    public function show(Articles $article): Response
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
    }
}
