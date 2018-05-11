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
}
