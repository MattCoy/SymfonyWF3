<?php

namespace App\Controller;

use App\Entity\User;
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
}
