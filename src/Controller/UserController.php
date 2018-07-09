<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserUploadImageType;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{

    /**
     * @Route("/usersList", name="usersList")
     */
    public function usersList()
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/{id}", name="userPage")
     */
    public function userPage(User $user)
    {

        return $this->render('user/page.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/edit/{id}", name="userEditPage")
     */
    public function userEditPage(User $user, Request $request, FileUploader $fileUploader)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if($request->files->get('image')){
            $fileName = $fileUploader->upload($request->files->get('image'), $user->getImage());
            $user->setImage($fileName);

            // maintenant, on peut supprimer l'article
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user
        ]);
    }
}
