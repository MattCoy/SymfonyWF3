<?php
/**
 * Created by PhpStorm.
 * User: Matthieu
 * Date: 15/05/2018
 * Time: 17:19
 */

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

//notre classe, le nom est au choix doit hériter de Doctrine\Bundle\FixturesBundle\Fixture
class TestData extends Fixture
{
    //pour pouvoir utiliser l'encode de password, on crée une propriété
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        //lors de l'instanciation de la classe, on va stocker l'encoder dans la propriété
        $this->encoder = $encoder;
    }
    //elle doit implémenter la méthode load()
    //on récupère l'ObjectManager qui va nous permettre de persister nos objets
    public function load(ObjectManager $manager)
    {
        //on va créer 10 catégories
        for($i=1; $i <= 10; $i++){

            $categorie = new Categorie();
            $categorie->setLibelle('catégorie ' . $i);
            $manager->persist($categorie);

        }

        //on crée 30 articles

        for($i=1; $i <= 30; $i++){

            $article = new Article();

            $article->setTitle('Titre ' . $i);

            $article->setContent('Un contenu vraiment très intéressant numéro ' . $i);

            //on va générer des dates aléatoirement :

            //Generate a timestamp using mt_rand.
            $timestamp = mt_rand(1, time());

            //Format that timestamp into a readable date string.
            $randomDate = date("Y-m-d H:i:s", $timestamp);

            //create DateTime Object
            $article->setDatePubli(new \DateTime($randomDate));

            //on crée un tableau d'auteurs et on en choisit un au hasard
            $auteurs = ['Verlaine', 'Hugo', 'Voltaire', 'Zola', 'Dumas', 'Duras', 'Molière', 'Ribéry' ];

            $article->setAuthor($auteurs[array_rand($auteurs)]);

            $manager->persist($article);

        }

        //on ajoute 5 utilisateurs
        for($i=1; $i<=5; $i++){
            $user = new User();
            $user->setUsername('toto' . $i);
            $user->setEmail('toto' . $i . '@toto.to');
            //on va mettre toto1 en admin
            if($user->getUsername() === 'toto1'){
                $user->setRoles(array('ROLE_USER', 'ROLE_ADMIN'));
            }
            else{
                //les autres sont de simples user
                $user->setRoles(array('ROLE_USER'));
            }
            //on définit un mot de passe
            $plainPassword = 'toto' . $i;
            //on l'encode
            $encoded = $this->encoder->encodePassword($user, $plainPassword);
            //on maj le user
            $user->setPassword($encoded);

            $manager->persist($user);
        }

        //ne pas oublier de faire flush
        $manager->flush();
    }
}