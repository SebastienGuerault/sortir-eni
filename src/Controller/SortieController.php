<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie")
     */
    public function afficherSortie(): Response
    {
        return $this->render('sortie/index.html.twig');
    }
    /**
     * @Route("/sortie/create", name="sortie_create", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', $sortie->getNom() . " a bien été créée !");

            return $this->redirectToRoute('sortie',[
            ]);
        }

        return $this->render('sortie/creationSortie.html.twig', [
            'form' => $form->createView(),
            'sortie' => $sortie,
        ]);
    }
}
