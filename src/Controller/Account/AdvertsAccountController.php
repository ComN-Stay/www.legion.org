<?php

namespace App\Controller\Account;

use App\Entity\Adverts;
use App\Form\Adverts1Type;
use App\Repository\AdvertsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account/adverts')]
class AdvertsAccountController extends AbstractController
{
   #[Route('/new', name: 'app_adverts_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $advert = new Adverts();
        $form = $this->createForm(Adverts1Type::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($advert);
            $entityManager->flush();

            return $this->redirectToRoute('app_adverts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/adverts/new.html.twig', [
            'advert' => $advert,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_adverts_show', methods: ['GET'])]
    public function show(Adverts $advert): Response
    {
        return $this->render('account/adverts/show.html.twig', [
            'advert' => $advert,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_adverts_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Adverts $advert, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Adverts1Type::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_adverts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/adverts/edit.html.twig', [
            'advert' => $advert,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_adverts_delete', methods: ['POST'])]
    public function delete(Request $request, Adverts $advert, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {
            $entityManager->remove($advert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_adverts_index', [], Response::HTTP_SEE_OTHER);
    }
}
