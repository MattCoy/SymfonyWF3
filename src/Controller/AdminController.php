<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleAdminType;
use App\Service\FileUploader;
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
    public function addArticle(Request $request, FileUploader $fileUploader)
    {
        $article = new Article();

        $form = $this->createForm(ArticleAdminType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
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
    public function updateArticle(Request $request, Article $article, FileUploader $fileUploader){

        //on stocke le nom du fichier image au cas où aucun fiochier n'ai été envoyé
        $fileName = $article->getImage();

        //on doit remplaçer le nom du fichier image par une instance de File représentant le fichier
        if($article->getImage()) {
            $article->setImage(
                new File($this->getParameter('image_directory') . '/' . $article->getImage())
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

                //on utilise notre service qui upload l'image, supprime l'ancienne image et renvoie le nom aléatoire
                $fileName = $fileUploader->upload($file, $fileName);

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
