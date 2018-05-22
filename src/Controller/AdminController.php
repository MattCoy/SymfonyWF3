<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdminController extends Controller
{
    /**
 * @Route("/admin", name="adminHome")
 */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/reserve/aux-auteurs", name="reserveAuteurs")
     */
    public function testReserve()
    {
        //si l'utilisateur n'a pas ROLE_AUTEUR, une erreur 403 est renvoyée
        $this->denyAccessUnlessGranted('ROLE_AUTEUR', null, 'Unable to access this page!');

        //si on est auteur, le reste du controleur est exécuté

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/reserve/aux-moderateurs", name="reserveModerateurs")
     *
     * //on peut le faire grâce aux annotations
     * @Security("has_role('ROLE_MODERATEUR')")
     */
    public function testReserve2()
    {
        //si on est moderateur, le controleur est exécuté

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
