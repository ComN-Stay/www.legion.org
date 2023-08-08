<?php

namespace App\Controller\Admin;

use App\Entity\PetsType;
use App\Form\PetsTypeType;
use App\Repository\PetsTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/pets_types')]
class PetsTypeAdminController extends AbstractController
{
    #[Route('/', name: 'app_pets_type_admin_index', methods: ['GET'])]
    public function index(PetsTypeRepository $petsTypeRepository): Response
    {
        return $this->render('admin/pets_type_admin/index.html.twig', [
            'pets_types' => $petsTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_pets_type_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $petsType = new PetsType();
        $form = $this->createForm(PetsTypeType::class, $petsType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($petsType);
            $entityManager->flush();
            $this->addFlash('success', 'Création effectuée');

            return $this->redirectToRoute('app_pets_type_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/pets_type_admin/new.html.twig', [
            'pets_type' => $petsType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pets_type_admin_show', methods: ['GET'])]
    public function show(PetsType $petsType): Response
    {
        return $this->render('admin/pets_type_admin/show.html.twig', [
            'pets_type' => $petsType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pets_type_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PetsType $petsType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PetsTypeType::class, $petsType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Modification effectuée');

            return $this->redirectToRoute('app_pets_type_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/pets_type_admin/edit.html.twig', [
            'pets_type' => $petsType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pets_type_admin_delete', methods: ['POST'])]
    public function delete(Request $request, PetsType $petsType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$petsType->getId(), $request->request->get('_token'))) {
            $entityManager->remove($petsType);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectuée');
        }

        return $this->redirectToRoute('app_pets_type_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
