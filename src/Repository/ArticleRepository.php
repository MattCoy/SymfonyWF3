<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /*
     * on écrit myFindAll() en faisant la jointure de façon à récupérer les infos de l'auteur
     * de l'article en une seule requête
     */
    public function myFindAll(){
        $querybuilder = $this->createQueryBuilder('a')
            //on fait la jointure
            // a.user fait référence à la propriété user de l'entité article
            ->innerJoin('a.user', 'u')
            // on récupère les données de l'utilisateur associé pour éviter des requêtes supp
            ->addSelect('u')
            //on trie par date de publication ... pourquoi pas
            ->orderBy('a.date_publi', 'DESC')
            ->getQuery();

        return $querybuilder->execute();
    }

    /*
     * Méthode qui va récupérer les articles dont la date de publication est plus récente
     * que la date donnée en paramètre
     * @param $date_post string, la date au format datetime
     * @return array of arrays of article data
     */
    public function findAllPostedAfter($date_post): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT a.id as idArticle, title, content, date_publi, u.* FROM article a INNER JOIN user u ON a.user_id = u.id 
        WHERE a.date_publi > :date_post
        ORDER BY a.date_publi ASC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['date_post' => $date_post]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /*
     * Cette Méthode fait la même chose que findAllPostedAfter()
     * mais on fait la requête en objet
     * @param $date_post string, la date au format datetime
     * @return array of article objects
     */
    public function findAllPostedAfter2($date_post): array
    {
        //avec cette méthode, l'objet $querybuilder crée sait automatiquement qu'il doit chercher
        //dans la table article, la première ligne permet de définir un alias, par convention a (la première lettre de la table)
        $querybuilder = $this->createQueryBuilder('a')
            // a.user fait référence à la propriété user de l'entité article
            ->innerJoin('a.user', 'u')
            // on récupère les données de l'utilisateur associé pour éviter des requêtes supp
            ->addSelect('u')
            ->andWhere('a.date_publi > :date_post')
            ->setParameter('date_post', $date_post)
            ->orderBy('a.date_publi', 'ASC')
            ->getQuery();

        return $querybuilder->execute();
    }

//    /**
//     * @return Article[] Returns an array of Article objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
