<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/campus-liste", name="campus_liste", methods={"GET"})
     */
    public function indexCampus(Request $request,CampusRepository $campusRepository): Response
    {
        // On récupère l'attribut {'page':1} de la requete GET (envoyé par le twig du navigateur)
        // l'attribut est toujours typé en string => le convertir en int et l'enregistrer dans la variable $page
        $page = (int)$request->query->get('page');

        // Récuperer la liste des villes avec la requête findPaginatedCities()
        // le paramètre $page change de valeur à chaque fois qu'on appuie sur le lien hypertexte de la page du tableau
        $paginatedCampus = $campusRepository->findPaginatedCampus($page);


        // Afficher la page  index.html.twig + retourner notre liste 'villes'
        return $this->render('campus/index.html.twig', [
            'campuss'=> $paginatedCampus

        ]);
    }

    /**
     * @Route("campus/modifier/{id}", name="campus_modifier", methods={"GET","POST"})
     */
    public function modifierCampus(Request $request, Campus $campus): Response
    {
        // Création du formulaire de campus
        $form = $this->createForm(CampusType::class, $campus);

        // Récupération des des données soumises par l'utilisateur et les injecter dans l'objet campus
        $form->handleRequest($request);

        // Si formulaire rempli correctement l'objet campus est injecté dans la BDD avec getDoctrine()->getManger()
        // + redirige vers la page de l'index des campus
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            // Message de succes affiché
            $this->addFlash('success', $campus->getNom() . " a bien été modifiée !");

            return $this->redirectToRoute('campus_liste');
        }


        // Redirection vers la page de l'index + retourne l'objet $campus récupérer dans la index
        // + création du formulaire affichant les données de la campus à modifier
        return $this->render('campus/modification.html.twig', [
            'campus' => $campus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("campus/supprimer/{id}", name="campus_supprimer", methods={"POST"})
     */
    public function supprimerCampus(Request $request, Campus $campus): Response
    {
        //si la ville est associée à des lieux ou a des utilisateurs, on ne peut pas la supprimer
        if (($campus->getSorties()->count()>0) && !empty($campus->getUsers())){
            $this->addFlash('warning', $campus->getNom() . " est associée à des sorties ou à des utilisateurs, et ne peut être supprimée !");
            return $this->redirectToRoute('campus_liste');
        }

       // if ($this->isCsrfTokenValid('delete'.$campus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($campus);
            $entityManager->flush();


        return $this->redirectToRoute('campus_liste');
    }

    /**
     * @Route("campus/afficher/{id}", name="campus_afficher", methods={"GET"})
     */
    public function afficherCampus(Campus $campus): Response
    {
        // récupère l'ID de l'objet campus affiché sur la liste de la page index
        // et retourne un twig qui affichera les infos du campus séléctionné
        return $this->render('campus/affichage.html.twig', [
            'campus' => $campus,
        ]);
    }

    /**
     * @Route("campus/ajouter", name="campus_ajouter", methods={"GET","POST"})
     */
    public function ajouterCampus(Request $request): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campus);
            $entityManager->flush();

            $this->addFlash('success', $campus->getNom() . " a bien été créé !");

            return $this->redirectToRoute('campus_liste');
        }

        return $this->render('campus/creation.html.twig', [
            'form' => $form->createView(),
            'campus' => $campus,
        ]);
    }

}
