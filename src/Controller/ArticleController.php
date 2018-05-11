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
    public function addArticle()
    {

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
                'No article found for id ' . $id
            );
        }

        return $this->render('article/article.html.twig', array('article' => $article));

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

        return $this->render('article/articles.html.twig', array('articles' => $articles));

    }

    /**
     * @Route("/articles-recents",
     *     name="articles_recents"
     * )
     */
    public function showRecents()
    {
        //on va appeler la méthode findAllPostedAfter() nouvellement créée dans notre repository
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAllPostedAfter('2000-01-01');
        //$articles est  un tableau de tableaux et non pas un tableau d'objets articles

        $articles2 = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAllPostedAfter2('2000-01-01');
        //articles2 est un tableau d'objets articles

        return $this->render('article/articles.recents.html.twig', array(
            'articles' => $articles,
            'articles2' => $articles2
        ));

    }

    /**
     * @Route("/article/update/{id}",
     *     name="article_update",
     *     requirements={"id":"\d+"}
     * )
     */
    public function updateArticle(Article $article)
    {
        // le ParamConverter convertit automatiquement l'id en objet Article

        //Ensuite je peut modifier mon article
        $article->setTitle('titre modifié');

        //récupération du manager
        $entityManager = $this->getDoctrine()->getManager();

        //ici pas besoin de faire $entityManager->persist($article);
        //car doctrine a déjà en mémoire cette entité, puisqu'il l'a récupéré dans la base

        $entityManager->flush();
        //à ce moment, doctrine sait que $article existe déjà dans la base et va donc faire un update au lieu d'un insert !

        //message flash
        $this->addFlash(
            'success',
            'Article modifié !'
        );

        //on redirige sur la liste des 5 derniers articles
        return $this->redirectToRoute('articles_showAll');
    }

    /**
     * @Route("/article/delete/{id}",
     *     name="article_delete",
     *     requirements={"id":"\d+"}
     * )
     */
    public function deleteArticle(Article $article)
    {
        // le ParamConverter convertit automatiquement l'id en objet Article

        //récupération du manager
        $entityManager = $this->getDoctrine()->getManager();

        //Je veux supprimer cet article
        $entityManager->remove($article);

        //j'execute les requêtes
        $entityManager->flush();
        $this->addFlash(
            'warning',
            'Article supprimé !'
        );

        //on redirige sur la liste des 5 derniers articles
        return $this->redirectToRoute('articles_showAll');
    }
}
