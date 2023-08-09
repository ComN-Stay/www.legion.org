<?php

namespace App\Controller\Admin;

use App\Entity\Adverts;
use App\Form\AdvertsType;
use App\Repository\AdvertsRepository;
use App\Repository\MediasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function show(Adverts $advert, MediasRepository $mediasRepository): Response
    {
        return $this->render('admin/adverts_admin/show.html.twig', [
            'advert' => $advert,
            'medias' => $mediasRepository->findBy(['fk_advert' => $advert->getId()])
        ]);
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
                $mediasRepository->remove($media);
                $mediasRepository->flush();
            }
            $this->addFlash('success', 'Annonce Supprimée');
        }

        return $this->redirectToRoute('app_adverts_admin_index', $parameters, Response::HTTP_SEE_OTHER);
    }
}
