<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PetitionsRepository;
use App\Form\PetitionsType;
use App\Entity\Petitions;

#[Route('/admin/petitions')]
class PetitionsAdminController extends AbstractController
{
    #[Route('/list/{status?}', name: 'app_petitions_admin_index', methods: ['GET'])]
    public function index(PetitionsRepository $petitionsRepository, $status, $publicUploadDir): Response
    {
        if($status !== null) {
            $petitions = $petitionsRepository->findBy(['status' => false]);
        } else {
            $petitions = $petitionsRepository->findAll();
        }
        return $this->render('admin/petitions_admin/index.html.twig', [
            'petitions' => $petitions,
            'mediaFolder' => $publicUploadDir
        ]);
    }

    #[Route('/activation', name: 'app_petitions_admin_activation', methods: ['GET', 'POST'])]
    public function activation(Request $request, PetitionsRepository $petitionsRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($request->isXMLHttpRequest()) {
            $res['result'] = 'error';
            $petitions = $petitionsRepository->find($request->request->get('id'));
            $petitions->setStatus($request->request->get('status'));
            $entityManager->persist($petitions);
            $res['result'] = 'success';
            $entityManager->flush();
            return new JsonResponse(json_encode($res));
        }
    
        return new Response('This is not ajax !', 400);
    }

    #[Route('/new', name: 'app_petitions_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $petition = new Petitions();
        $form = $this->createForm(PetitionsType::class, $petition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($petition);
            $entityManager->flush();

            return $this->redirectToRoute('app_petitions_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/petitions_admin/new.html.twig', [
            'petition' => $petition,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_petitions_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Petitions $petition, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$petition->getId(), $request->request->get('_token'))) {
            $entityManager->remove($petition);
            $entityManager->flush();
            $this->addFlash('success', 'Pétition supprimée');
        }

        return $this->redirectToRoute('app_petitions_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
