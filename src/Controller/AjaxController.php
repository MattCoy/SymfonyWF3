<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AjaxController extends Controller
{
    /**
     * @Route("/ajax/search/articles/by/userid/{id}", name="ajax-search-articles-by-user", requirements={"id"="\d+"})
     */
    public function searchArticlesByUserId(User $user)
    {
        //je récupère l'utilisateur grâce à l'id reçue dans le placeholder
        //je peux utiliser une méthode dynamique findBy'Propriete'() générique fournie par doctrine
        //on rajoute à la suite de findBy le nom de la propriété par laquelle on fait la recherche et doctrine va comprendre et faire la requête
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findByUser($user);

        return $this->render('ajax/articles.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/ajax/search/articles/by/userid2/", name="ajax-search-articles-by-user2")
     */
    public function searchArticlesByUserId2(Request $request)
    {
        //on peut faire un dump qui sera accessible dans le web profiler
        dump($request->request->all());
        //on doit d'abord vérifier que le paramètre existe et est numérique
        if(empty($request->request->get('idUser')) || !preg_match('#\d+#', $request->request->get('idUser'))){
            //idUser invalide, on renvoie une réponse json
            return $this->json(array('status' => 'ko'));
        }

        $user =  $this->getDoctrine()
            ->getRepository(User::class)
            ->findById($request->request->get('idUser'));

        $articles =  $this->getDoctrine()
            ->getRepository(Article::class)
            ->findByUser($user);
        //dump($articles);
        //va renvoyer les articles au format json
        //on les passe par une boucle pour les mettre dans un tableau
        foreach ($articles as $article) {
            $results[] = [
                'title' => $article->getTitle(),
                'datepubli' => $article->getDatePubli()->format('d/m/Y'),
                'author' => $article->getUser()->getUsername(),
                'content' => $article->getContent(),
                'url' => $this->generateUrl('article_show', ['id' => $article->getId()]),
            ];
        }
        return $this->json(array('status' => 'ok', 'articles' => $results));
    }
}
