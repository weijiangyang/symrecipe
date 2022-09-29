<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connection', name: 'security_login',methods:['GET','POST'])]
    public function index(AuthenticationUtils $utils): Response
    {
        $lastUsername = $utils->getLastUsername();
        $error = $utils->getLastAuthenticationError();
        return $this->render('pages/security/login.html.twig', [
            'last_username'=>$lastUsername,
            'error'=>$error
        ]);
    }
    #[Route('/deconnection', name: 'security_logout', methods: ['GET', 'POST'])]
    public function logout(){

    }
}
