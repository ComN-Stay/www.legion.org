<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MediasRepository;
use App\Repository\AdvertsRepository;
use App\Form\AdvertsType;
use App\Entity\Adverts;

#[Route('/admin/adverts')]
class AdvertsAdminController extends AbstractController
{
    #[Route('/list/{status?}', name: 'app_adverts_admin_index', methods: ['GET'])]
    public function index(AdvertsRepository $advertsRepository, $status): Response
    {
        if($status) {
            $adverts = $advertsRepository->findBy(['status' => false]);
        } else {
            $adverts = $advertsRepository->findAll();
        }
        return $this->render('admin/adverts_admin/index.html.twig', [
            'adverts' => $adverts,
            'page_title' => ($status == null) ? 'Toutes les annonces' : 'Annonces en attente de validation'
        ]);
    }

    #[Route('/{id}', name: 'app_adverts_admin_show', methods: ['GET'])]
    public function show(Adverts $advert, MediasRepository $mediasRepository, $publicUploadDir): Response
    {
        return $this->render('admin/adverts_admin/show.html.twig', [
            'advert' => $advert,
            'medias' => $mediasRepository->findBy(['fk_advert' => $advert->getId()]),
            'mediaFolder' => $publicUploadDir
        ]);
    }

    #[Route('/activation', name: 'app_adverts_admin_activation', methods: ['GET', 'POST'])]
    public function activation(Request $request, AdvertsRepository $advertsRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($request->isXMLHttpRequest()) {
            $res['result'] = 'error';
            $advert = $advertsRepository->find($request->request->get('id'));
            $advert->setStatus($request->request->get('status'));
            $entityManager->persist($advert);
            $res['result'] = 'success';
            $entityManager->flush();
            return new JsonResponse(json_encode($res));
        }
    
        return new Response('This is not ajax !', 400);
    }

    #[Route('/update/{id}', name: 'app_adverts_admin_update', methods: ['GET'])]
    public function validate(Adverts $advert, EntityManagerInterface $entityManager): Response
    {
        
        $advert->setStatus(true);
        $entityManager->persist($advert);
        $entityManager->flush();
        $this->addFlash('success', 'Annonce validée');
        return $this->redirectToRoute('app_adverts_admin_index', ['status' => 1], Response::HTTP_SEE_OTHER);
        
    }

    #[Route('/{id}', name: 'app_adverts_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Adverts $advert, EntityManagerInterface $entityManager, MediasRepository $mediasRepository, $kernelUploadDir): Response
    {
        $parameters = ($advert->isStatus() == true) ? [] : ['status' => 1];
        $medias = $mediasRepository->findBy(['fk_advert' => $advert->getId()]);
        if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {
            $entityManager->remove($advert);
            $entityManager->flush();
            foreach($medias as $media) {
                @unlink($kernelUploadDir . '/' . $media->getFilename());
            }
            $this->addFlash('success', 'Annonce Supprimée');
        }

        return $this->redirectToRoute('app_adverts_admin_index', $parameters, Response::HTTP_SEE_OTHER);
    }
}
