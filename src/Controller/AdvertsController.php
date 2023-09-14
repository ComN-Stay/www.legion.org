<?php

namespace App\Controller;

use App\Entity\Adverts;
use App\Form\Adverts1Type;
use Pagerfanta\Pagerfanta;
use App\Repository\StatusRepository;
use App\Repository\AdvertsRepository;
use App\Repository\PetsTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/adverts')]
class AdvertsController extends AbstractController
{
    #[Route('/', name: 'app_adverts_index', methods: ['GET', 'POST'])]
    public function index(
        AdvertsRepository $advertsRepository,
        StatusRepository $statusRepository,
        PetsTypeRepository $petsTypeRepository,
        Request $request,
    ): Response
    {
        $session = $request->getSession();
        $petsTypes = $petsTypeRepository->findAll();
        $status = $statusRepository->find(3);
        $localization = null;
        $types = null;
        $min_price = null;
        $max_price = null;
        $order_by = 'date_add';
        $order_way = 'DESC';
        if($session->get('order') == null) {
            $session->set('order', 'date_add-DESC');
        }
        
        if($request->getMethod() == 'POST') {
            $localization = ($request->request->get('localization') != '') ? $request->request->get('localization') : null;
            $session->set('localization', $localization);
            $types = ($request->request->all('fk_type') != null) ? $request->request->all('fk_type') : null;
            $session->set('types', $types);
            $min_price = ($request->request->get('min_price') != '') ? $request->request->get('min_price') : (($request->request->get('max_price') != '') ? 0 : null);
            $session->set('min_price', $min_price);
            $max_price = ($request->request->get('max_price') != '') ? $request->request->get('max_price') : null;
            $session->set('max_price', $max_price);
            $session->set('order', $request->request->get('order'));
            $exp = explode('-', $request->request->get('order'));
            $order_by = $exp[0];
            $order_way = $exp[1];
        }
        
        $queryBuilder = $advertsRepository->createOrderedByDateQueryBuilder($status, $localization, $types, $min_price, $max_price, $order_by, $order_way);
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('page', 1),
            10
        );
        
        return $this->render('front/adverts/index.html.twig', [
            'pager' => $pagerfanta,
            'types' => $petsTypes,
        ]);
    }

    #[Route('/reset', name: 'app_adverts_reset', methods: ['GET'])]
    public function reset(
        Request $request
    ): Response
    {
        $session = $request->getSession();
        $session->set('order', 'date_add-DESC');
        $session->set('localization', null);
        $session->set('types', null);
        $session->set('min_price', null);
        $session->set('max_price', null);

        return $this->redirectToRoute('app_adverts_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_adverts_show', methods: ['GET'])]
    public function show(
        Adverts $advert, 
        $googleApiKey
    ): Response
    {
        return $this->render('front/adverts/show.html.twig', [
            'advert' => $advert,
            'googleKey' => $googleApiKey
        ]);
    }
}
