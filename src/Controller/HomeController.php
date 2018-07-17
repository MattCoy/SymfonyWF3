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
use App\Entity\Article;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//pour pouvoir utiliser les annotations
use Symfony\Component\Routing\Annotation\Route;

//le nom de notre classe
class HomeController extends Controller
{
    /**
     * @Route("/",
     *     name="home"
     * )
     */
    public function home(){

        $users = $articles = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('index.html.twig',
                                    array('users' => $users)
        );
    }

    /**
     * @Route("/bonjour/", name="bonjour")
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
     * cette route va rediriger vers home
     *
     * @Route("/testredirect/", name="redirectHome")
     *
     */
    public function redirectHome(){
        //on peut imaginer que l'on fait un traitement ici
        // par exemple enregistrer un nouvel article ou produit...
        //puis rediriger vers une autre page
        //la route doir être nommée: on nomme la route / "home"

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/exercice1/comment-allez-vous", name="exercice1")
     */
    public function caVa(){

        //on envoie une réponse
        return new Response('<html><body><strong>Bien , merci !</strong></body></html>');
    }

    /**
     * @Route("/exercice2/heure", name="exercice2")
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
        //la récupération des variables ne se fait pas par l'ordre mais par le nom de la variable
        //qui doit correspondre au nom du placeholder
        return $this->render('exercice3.html.twig',
            array('age' => $age,
                  'pseudo' => $pseudo
            )
        );
    }

    /**
     * page qui va afficher les infos de l'utilisateur connecté
     * @Route("/user-info/",
     *      name="userInfo"
     * )
     */
    public function showUser(){

        //pour vérifier qu'un utilisateur est bien connecté (= pas anonyme)
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        //récupérer l'objet user
        $moi = $this->getUser();

        //ajouter un article depuis un objet User
        $article = new Article();
        $article->setTitle('test');
        $article->setContent('test contenu');
        $article->setDatePubli(new \DateTime(date('Y-m-d H:i:s')));

        //on ajoute l'article tout en le lien à l'utilisateur (ici $moi)
        $moi->addArticle($article);

        $entityManager = $this->getDoctrine()->getManager();
        //on persiste l'article puisque c'est un nouvel article
        $entityManager->persist($article);
        $entityManager->flush();


        //supprimer un article depuis l'objet User
        $moi->removeArticle($article);

        $entityManager->flush();

        return $this->render('user.info.html.twig',
                                array('moi' => $moi)
        );
    }

    /**
     * page de test : utilisation de l'objet request
     * @Route("/test-request/",
     *      name="testRequest"
     * )
     */
    public function testRequest(Request $request){

        //pour accéder à $_GET
        $get = $request->query->all();
        //si on attend un paramètre, par exemple ?message=bonjour toto
        //le premier paramètre de la méthode get() correspond au nom du paramètre d'url et le second correspond à sa valeur s'il n'existe pas
        $message = $request->query->get('message', 'pas de message');

        //on peut utiliser la fonction dump() (var_dump() amélioré) pour débugger des variables
        dump($get);

        //permet de simuler une requête (ici en post)
        $request = Request::create(
            '/test-request/',
            'POST',
            array('pseudo' => 'pompom')
        );

        $post = $request->request->all();
        $pseudo = $request->request->get('pseudo');
        dump($post);

        return $this->render('test.request.html.twig',
            array('message' => $message, 'pseudo' => $pseudo)
        );
    }

}