<?php

namespace App\Controller;



use App\Entity\User;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class ProfileController extends AbstractController
{

    /**
     * Modification du profil d'un utilisateur
     *
     * @Route("/profile/modification", name="profile_mofication")
     */

    public function modifierProfil(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

//    Méthode vérification des champs du formulaire
//        if ($form->isSubmitted()) {
//            dump($form->isValid());
//            dump($form->get('new_password')->isValid());
//            die();
//        }

        if ($form->isSubmitted() && $form->isValid()) {


            // encodage : hashache du password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('new_password')->getData()
                )
            );

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profil modifié !');

            // redirection vers la route de déconnexion
            return $this->redirectToRoute('main_home');
            // Normalement redirection vers 'profile_affichage' ?
        }

        return $this->render('user/profileModif.html.twig',
        [
            'registrationForm' => $form->createView(),
        ]);

    }
    /**
     * Affichage du profil d'un utilisateur
     *
     * @Route("/profile/affichage", name="profile_affichage")
     */

    public function afficherProfil(Request $request): Response
    {
        $user= $this->getUser();
         return $this->render('user/profile.html.twig',[
             'user'=>$user,
         ]);

    }



}
