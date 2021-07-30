<?php

namespace App\Controller;



use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProfileController extends AbstractController
{

    /**
     * Modification du profil d'un utilisateur
     *
     * @Route("/modification", name="profile_edit")
     */

    public function edit(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($form->isValid()){
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Profil modifié !');
            }
            else {
                $em->refresh($user);
            }
        }

        return $this->render('user/profile.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

    }


}
