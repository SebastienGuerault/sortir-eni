<?php

namespace App\Controller;



use App\Entity\User;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


class ProfileController extends AbstractController
{

    /**
     * Modification du profil d'un utilisateur
     *
     * @Route("/profile/modification", name="profile_mofication")
     */

    public function modifierProfil(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//                    $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Profil modifié !');

                return $this->redirectToRoute('main_home');
        }

        return $this->render('main/home.html.twig');

    }
    /**
     * Affichage du profil d'un utilisateur
     *
     * @Route("/profile/affichage", name="profile_affichage")
     */

    public function afficherProfil(Request $request): Response
    {
         return $this->render('user/profile.html.twig');

    }



}
