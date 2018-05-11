<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategorieController extends Controller
{
    /**
     * @Route("/categorie/add", name="categorie_add")
     */
    public function addCategorie()
    {
        $entityManager = $this->getDoctrine()->getManager();

        //je vais créer un boucle pour ajouter 10 catégories test
        for ($i = 1; $i <= 10; $i++) {

            $categorie = new Categorie();

            $categorie->setLibelle('catégorie' . $i);

            //je met en mémoire mes objets, pour l'instant aucune requête n'est exécutée
            $entityManager->persist($categorie);

        }

        //on dit à doctrine d'exécuter toutes les requêtes
        //les catégories sont insérées dans la table
        $entityManager->flush();

        return $this->render('categorie/categorie.add.html.twig');
    }


    /**
     * @Route("/categorie/last5", name="categorie_last5")
     */
    public function showLast5Categories()
    {

        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findLast5();

        return $this->render('categorie/categorie.last5.html.twig', ['categories' => $categories]);
    }

    /**
     * @Route("/categorie/update/{id}",
     *     name="categorie_update",
     *     requirements={"id":"\d+"}
     * )
     */
    public function updateCategorie(Categorie $categorie)
    {
        /* Ici on utilise une fonctionnalité très utile de symfony : le ParamConverter
        Alors que l'on devrait récupérer en argument $id, l'id de la catégorie,
        on indique à Symfony que l'on veut récupérer l'entité Categorie:
        le ParamConverter va comprendre cela et va lui même faire la requête find($id),
        ce qui nous évite d'écrire:
        $article = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->find($id);
        Si aucun article n'est trouvé, une erreur 404 est générée.
        */

        //Ensuite je peut modifier ma catégorie
        $categorie->setLibelle('catégorie modifiée');

        //récupération du manager
        $entityManager = $this->getDoctrine()->getManager();

        //ici pas besoin de faire $entityManager->persist($categorie);
        //car doctrine a déjà en mémoire cette entité, puisqu'elle l'a récupéré dans la base

        $entityManager->flush();
        //à ce moment, doctrine sait que $categorie existe déjà dans la base et va donc faire un update au lieu d'un insert !

        //On va utiliser une autre fonctionnalité très utile de Symfony : les messages flashs:
        // Ce sont des messages stockés en session et qui sont ensuite effaçés dès qu'ils sont affichés,
        //de sorte qu'il n'apparaissent qu'une seule fois
        $this->addFlash(
            'success',
            'Catégorie modifiée !'
        );

        //on redirige sur la liste des 5 dernières catégories
        return $this->redirectToRoute('categorie_last5');
    }

    /**
     * @Route("/categorie/delete/{id}",
     *     name="categorie_delete",
     *     requirements={"id":"\d+"}
     * )
     */
    public function deleteCategorie(Categorie $categorie)
    {
        // le ParamConverter convertit autoamtiquement l'id en objet Categorie

        //récupération du manager
        $entityManager = $this->getDoctrine()->getManager();

        //Je veux supprimer cette catégorie
        $entityManager->remove($categorie);

        //j'oexecute les requêtes
        $entityManager->flush();
        $this->addFlash(
            'warning',
            'Catégorie supprimée !'
        );

        //on redirige sur la liste des 5 dernières catégories
        return $this->redirectToRoute('categorie_last5');
    }
}
