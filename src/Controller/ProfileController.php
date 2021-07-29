<?php

namespace App\Controller;



use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */

class ProfileController extends AbstractController
{

    /**
     * Affichage du profil d'un utilisateur
     *
     * @Route("/{id}", name="app_profile", requirements={"id": "\d+"})
     */

    public function profile(User $user): Response
    {

    }


}
