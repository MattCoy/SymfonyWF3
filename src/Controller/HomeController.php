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
     * @Route("/bonjour/")
     */
    //déclaration de notre méthode
    public function bonjour(){
        //ici on écrira du code

        //on envoie une réponse : on affiche bonjour avec un peu de html
        return new Response('<html><body><strong>Bonjour !</strong></body></html>');
    }

    /**
     * cette route va matcher /bonjour/nimportequeltexte
     *
     * @Route("/bonjour/{nom}", name="bonjourNom" , requirements={"nom"="[a-z]+"})
     *
     * J'ai nommé ma route, ce qui me sera utile pour générer l'url ou faire des redirections
     */
    public function bonjour2($nom){
        //$nom est automatiquement envoyé en paramètre à notre méthode
        //et contiendra tout ce qui suit /bonjour/

        return $this->render('bonjour.html.twig', array('nom'=>$nom));
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

    /**
     * @Route("/exercice3/{age}/{pseudo}",
     *      name="exercice3",
     *     requirements={
     *          "age" : "\d+",
     *          "pseudo" : "\w+"
     *     }
     * )
     */
    public function bonjourPseudoAge($pseudo, $age){
        return $this->render('exercice3.html.twig',
            array('age' => $age,
                  'pseudo' => $pseudo
            )
        );
    }
}