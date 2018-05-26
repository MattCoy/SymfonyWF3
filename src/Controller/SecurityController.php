<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {

        // récupération des éventuelles erreurs de login
        $error = $authenticationUtils->getLastAuthenticationError();

        // récupération du nom d'utilisateur (pour pré remplir le champ en cas d'erreur)
        $lastUsername = $authenticationUtils->getLastUsername();

        //on place la vue dans le sous-dossier templates/security/
        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        //on crée un objet user vide
        $user = new User();

        //on crée le formulaire à partir de notre classe
        $form = $this->createForm(UserType::class, $user);

        //on demande au formulaire de traiter les données envoyées par l'utilisateur
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user = $form->getData();

            //on spécifie le rôle par défaut
            $user->setRoles(['ROLE_USER']);

            //on encode le mot de passe
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            //on maj le user
            $user->setPassword($encoded);

            //on supprime le mdp en clair
            $user->setPlainPassword('');

            // maintenant, on peut enregistrer ce nouvel utilisateur
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //on crée un message flash
            $this->addFlash(
                'success',
                'Vous êtes bien inscrit, vous pouvez vous connecter !'
            );

            //on renvoie sur la liste des catégories par exemple
            return $this->redirectToRoute('login');

        }

        //on place la vue dans le sous-dossier templates/security/
        return $this->render('security/register.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
