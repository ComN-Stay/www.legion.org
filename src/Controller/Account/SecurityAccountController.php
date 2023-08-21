<?php

namespace App\Controller\Account;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\UserType;
use App\Entity\User;

#[Route(path: '/account')]
class SecurityAccountController extends AbstractController
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }
    
    #[Route(path: '/login', name: 'account_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('target_path');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('account/account_login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'account_logout')]
    public function logout(): Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
}
