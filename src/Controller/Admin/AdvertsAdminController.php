<?php

namespace App\Controller\Admin;

use App\Entity\Adverts;
use App\Service\MailService;
use App\Repository\MediasRepository;
use App\Repository\StatusRepository;
use App\Repository\AdvertsRepository;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/adverts')]
class AdvertsAdminController extends AbstractController
{
    #[Route('/list/{status?}', name: 'app_adverts_admin_index', methods: ['GET'])]
    public function index(
        AdvertsRepository $advertsRepository, 
        StatusRepository $statusRepository,
        $status
    ): Response
    {
        $pending = $statusRepository->find(2);
        $online = $statusRepository->find(3);
        if($status) {
            $adverts = $advertsRepository->findBy(['fk_status' => $pending]);
        } else {
            $adverts = $advertsRepository->findBy(['fk_status' => $online]);
        }
        return $this->render('admin/adverts_admin/index.html.twig', [
            'adverts' => $adverts,
            'page_title' => ($status == null) ? 'Annonces en ligne' : 'Annonces en attente de validation',
            'sidebar' => 'ads'
        ]);
    }
    
    #[Route('/activation', name: 'app_adverts_admin_activation', methods: ['GET', 'POST'])]
    public function activation(
        Request $request, 
        AdvertsRepository $advertsRepository, 
        EntityManagerInterface $entityManager,
        StatusRepository $statusRepository,
        CompanyRepository $companyRepository,
        MailService $mail,
    ): JsonResponse
    {
        if ($request->isXMLHttpRequest()) {
            $res['result'] = 'error';
            $advert = $advertsRepository->find($request->request->get('id'));
            $advert->setFkStatus($statusRepository->find($statusRepository->find(3)));
            $entityManager->persist($advert);
            $res['result'] = 'success';
            $entityManager->flush();
            $company = $companyRepository->find($advert->getFkCompany());
            $mail->sendMail([
                'to' => $company->getEmail(),
                'tpl' => 'active_advert',
                'vars' => [
                    'adverts' => $advert,
                    ]
                ]);
                return new JsonResponse(json_encode($res));
            }
                
        return new Response('This is not ajax !', 400);
    }
            
    #[Route('/rejected', name: 'app_adverts_admin_rejected', methods: ['GET', 'POST'])]
    public function rejected(
        Request $request, 
        AdvertsRepository $advertsRepository, 
        EntityManagerInterface $entityManager,
        StatusRepository $statusRepository,
        CompanyRepository $companyRepository,
        MailService $mail,
    ): JsonResponse
    {
        if ($request->isXMLHttpRequest()) {
            $res['result'] = 'error';
            $advert = $advertsRepository->find($request->request->get('id'));
            $advert->setFkStatus($statusRepository->find(4));
            $entityManager->persist($advert);
            $res['result'] = 'success';
            $entityManager->flush();
            $company = $companyRepository->find($advert->getFkCompany());
            $mail->sendMail([
                'to' => $company->getEmail(),
                'tpl' => 'advert_rejected',
                'vars' => [
                    'adverts' => $advert,
                ]
            ]);
            return new JsonResponse(json_encode($res));
        }
    
        return new Response('This is not ajax !', 400);
    }

    #[Route('/{id}', name: 'app_adverts_admin_show', methods: ['GET'])]
    public function show(Adverts $advert, MediasRepository $mediasRepository, $publicUploadDir): Response
    {
        return $this->render('admin/adverts_admin/show.html.twig', [
            'advert' => $advert,
            'medias' => $mediasRepository->findBy(['fk_advert' => $advert->getId()]),
            'mediaFolder' => $publicUploadDir,
            'sidebar' => 'ads'
        ]);
    }

    #[Route('/{id}', name: 'app_adverts_admin_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Adverts $advert, 
        EntityManagerInterface $entityManager, 
        MediasRepository $mediasRepository, 
        CompanyRepository $companyRepository,
        MailService $mail,
        $kernelUploadDir
    ): Response
    {
        $parameters = ($advert->getFkStatus() == 3) ? [] : ['status' => 1];
        $medias = $mediasRepository->findBy(['fk_advert' => $advert->getId()]);
        $company = $companyRepository->find($advert->getFkCompany());
        $current = $advert;
        if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {
            $entityManager->remove($advert);
            $entityManager->flush();
            foreach($medias as $media) {
                @unlink($kernelUploadDir . '/' . $media->getFilename());
                $entityManager->remove($media);
            }
            $this->addFlash('success', 'Annonce Supprimée');
            $mail->sendMail([
                'to' => $company->getEmail(),
                'tpl' => 'active_advert',
                'vars' => [
                    'advert' => $current,
                ]
            ]);
        }

        return $this->redirectToRoute('app_adverts_admin_index', $parameters, Response::HTTP_SEE_OTHER);
    }
}
