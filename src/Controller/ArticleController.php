<?php

namespace App\Controller;

// ce use nous permet d'utiliser new Article() dans ce namespace
use App\Entity\Article;

use App\Form\ArticleUserType;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ArticleController extends Controller
{
    /**
     *
     * @Route("/article/add", name="addArticle")
     *
     */
    public function addArticle(Request $request, FileUploader $fileUploader)
    {
        //seul un utilisateur connecté peut poster un article
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $article = new Article();
        $form = $this->createForm(ArticleUserType::class, $article);
        $form->handleRequest($request);
        //si le formulaire a été envoyé et si les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {

            // ici on charge le formulaire de remplir notre objet article avec ces données
            $article = $form->getData();

            // $files va contenir l'image envoyée
            $file = $article->getImage();

            //comme on permet à nos utilisateurs de ne pas envoyer d'image
            //on initialise $fileName
            $fileName = '';

            //si on a bien un fichier, on utilise notre service d'upload
            // upload l'image et renvoie le nom aléatoire
            if($file){
                $fileName = $fileUploader->upload($file);
            }

            // on met à jour la propriété image, qui doit contenir le nom
            // et pas l'image elle même
            $article->setImage($fileName);

            //l'utilisateur connecté est l'auteur
            $article->setUser($this->getUser());
            //la date de publication
            $article->setDatePubli(new \DateTime(date('Y-m-d H:i:s')));
            // maintenant, on peut supprimer l'article
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            //on crée un message flash
            $this->addFlash(
                'success',
                'Article ajouté !'
            );
            //on renvoie sur la liste des catégories par exemple
            return $this->redirectToRoute('articles_showAll');
        }
        return $this->render('article/add.html.twig', array(
            'form' => $form->createView(),
        ));
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
            ->myFindAll();

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
    public function updateArticle(Article $article, Request $request, FileUploader $fileUploader)
    {
        //on vérifie que l'utilisateur peut modifier l'article = s'il en est l'auteur
        $this->denyAccessUnlessGranted('edit', $article);

        //on stocke le nom du fichier image au cas où aucun fichier n'ai été envoyé
        $fileName = $article->getImage();

        //on doit remplaçer le nom du fichier image par une instance de File représentant le fichier
        if($article->getImage()) {
            $article->setImage(
                new File($this->getParameter('image_directory') . '/' . $article->getImage())
            );
        }

        $form = $this->createForm(ArticleUserType::class, $article);

        $form->handleRequest($request);

        //si le formulaire a été envoyé et si les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {

            // ici on charge le formulaire de remplir notre objet article avec ces données
            $article = $form->getData();

            if($article->getImage()) { //on ne fait le traitement de l'upload que si une image a été envoyée

                // $files va contenir l'image envoyée
                $file = $article->getImage();

                //on utilise notre service qui upload l'image, supprime l'ancienne image et renvoie le nom aléatoire
                $fileName = $fileUploader->upload($file, $fileName);

            }

            // on met à jour la propriété image, qui doit contenir le nom
            // et pas l'image elle même
            $article->setImage($fileName);

            // maintenant, on peut enregistrer ce nouvel article
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            //on crée un message flash
            $this->addFlash(
                'success',
                'Article modifié !'
            );

            //on renvoie sur la liste des catégories par exemple
            return $this->redirectToRoute('articles_showAll');
        }

        return $this->render('article/update.html.twig', array(
            'form' => $form->createView(),
            'image' => $fileName
        ));

    }

    /**
     * @Route("/article/delete/{id}",
     *     name="article_delete",
     *     requirements={"id":"\d+"}
     * )
     */
    public function deleteArticle(Article $article)
    {
        //on vérifie que l'utilisateur peut supprimer l'article = s'il en est l'auteur
        $this->denyAccessUnlessGranted('delete', $article);

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
