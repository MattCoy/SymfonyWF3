<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {

        // récupération des éventuelles erreurs de login
        $error = $authenticationUtils->getLastAuthenticationError();

        // récupération du nom d'utilisateur (pour pré remplir le champ en cas d'erreur)
        $lastUsername = $authenticationUtils->getLastUsername();

        //on place la vue dans le sous-dossier templates/security/
        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }
}
