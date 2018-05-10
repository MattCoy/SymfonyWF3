<?php

namespace App\Controller;

// ce use nous permet d'utiliser new Article() dans ce namespace
use App\Entity\Article;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ArticleController extends Controller
{

    /**
     *
     * @Route("/article/add", name="addArticle")
     *
     */
    public function addArticle(){

        //$entityManager est l'objet qui va nous permettre d'enregistrer des infos dans la base
        $entityManager = $this->getDoctrine()->getManager();

        //pour l'instant, on crée notre objet article en dur, on verra plus tard les formulaires
        //nous avons besoin d'instancier notre class Article, donc ne pas oublier le use pour pouvoir faire
        $article = new Article();
        $article->setTitle('Mon premier Article');
        $article->setContent('Rien d\'intéressant');
        $article->setAuthor('Moi');
        //nous avons déclaré notre propriété en datetime, on doit y stocker un objet de classe DateTime
        $date_publi = new \DateTime(date('Y-m-d H:i:s'));
        $article->setDatePubli($date_publi);

        //ici on dit à doctrine de conserver en mémoire cet objet
        //il n'est pas pour l'instant enregistré dans la table
        $entityManager->persist($article);

        //on dit à doctrine d'exécuter toutes les requêtes (ici une seule)
        $entityManager->flush();

        return $this->render('article/add.html.twig');
    }

    /**
     * @Route("/article/{id}",
     *     name="article_show",
     *     requirements={"id":"\d+"}
     * )
     */
    public function show($id)
    {
        // $this->getDoctrine()
        //            ->getRepository(Article::class)
        //est la classe qui nous permet de manipuler l'entité article (= table article dans la base)
        //find($id) récupère une entrée par son id
        //$article est un objet de classe Article
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        //nous permet de renvoyer un message d'erreur si aucun id ne correspond
        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id '.$id
            );
        }

        return $this->render('article/article.html.twig', array('article'=>$article));

    }

    /**
     * @Route("/articles",
     *     name="articles_showAll"
     * )
     */
    public function showAll()
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        if (!$articles) {
            throw $this->createNotFoundException(
                'No article found in database'
            );
        }

        return $this->render('article/articles.html.twig', array('articles'=>$articles));

    }
}
