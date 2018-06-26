<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleAdminType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
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
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->myFindAll();

        return $this->render('admin/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
 * @Route("/admin/article/add", name="adminAddArticle")
 */
    public function addArticle(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleAdminType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $article = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            //on crée un message flash
            $this->addFlash(
                'success',
                'Article ajouté !'
            );

            //on renvoie sur la liste des catégories par exemple
            return $this->redirectToRoute('adminHome');
        }

        return $this->render('admin/add.article.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article/update/{id}",
     *      name="adminUpdateArticle",
     *      requirements={"id":"\d+"}
     *     )
     */
    public function updateArticle(Request $request, Article $article){

        //on stocke le nom du fichier image au cas où aucun fiochier n'ai été envoyé
        $fileName = $article->getImage();

        //on doit remplaçer le nom du fichier image par une instance de File représentant le fichier
        if($article->getImage()) {
            $article->setImage(
                new File($this->getParameter('articles_image_directory') . '/' . $article->getImage())
            );
        }

        $form = $this->createForm(ArticleAdminType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // ici on charge le formulaire de remplir notre objet article avec ces données
            $article = $form->getData();

            if($article->getImage()) { //on ne fait le traitement de l'upload que si une image a été envoyée

                // $files va contenir l'image envoyée
                $file = $article->getImage();

                //on génère un nouveau nom
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                //on transfère le fichier sur le serveur
                $file->move(
                    $this->getParameter('articles_image_directory'),
                    $fileName
                );

            }

            // on met à jour la propriété image, qui doit contenir le nom
            // et pas l'image elle même
            $article->setImage($fileName);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            //on crée un message flash
            $this->addFlash(
                'success',
                'Article modifié !'
            );

            //on renvoie sur la liste des catégories par exemple
            return $this->redirectToRoute('adminHome');
        }

        return $this->render('admin/add.article.html.twig', [
            'form' => $form->createView(),
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
