<?php
/**
 * Created by PhpStorm.
 * User: Matthieu
 * Date: 04/05/2018
 * Time: 13:54
 */
//on déclare le namespace
namespace App\Controller;

//le use pour la classe Response que l'on utilise dans la méthode
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
//pour pouvoir utiliser les annotations
use Symfony\Component\Routing\Annotation\Route;

//le nom de notre classe
class HomeController extends Controller
{
    /**
     * @Route("/")
     */
    public function home(){
        $nom = 'toto';
        //on envoie une réponse
        return $this->render('index.html.twig',
                                    array('nom' => $nom)
        );
    }

    /**
     * @Route("/bonjour")
     */
    //déclaration de notre méthode
    public function bonjour(){
        //ici on écrira du code

        //on envoie une réponse : on affiche bonjour avec un peu de html
        return new Response('<html><body><strong>Bonjour !</strong></body></html>');
    }

    /**
     * @Route("/exercice1/comment-allez-vous")
     */
    public function caVa(){

        //on envoie une réponse
        return new Response('<html><body><strong>Bien , merci !</strong></body></html>');
    }

    /**
     * @Route("/exercice2/heure")
     */
    public function quelleHeure(){
        $date = new \DateTime(date('Y-m-d H:i:s'));
        //on envoie une réponse
        return $this->render('exercice2.html.twig',
            array('maDate' => $date->format('H\hs'))
        );
    }
}