<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class VilleController extends AbstractController
{

    /**
     * @Route("/ville-liste", name="ville_liste", methods={"GET"})
     */
    public function index(Request $request, VilleRepository $villeRepository): Response
    {
        // On récupère l'attribut {'page':1} de la requete GET (envoyé par le twig du navigateur)
        // l'attribut est toujours typé en string => le convertir en int et l'enregistrer dans la variable $page
        $page= (int)$request->query->get('page');

        // Récuperer la liste des villes avec la requête findPaginatedCities()
        // le paramètre $page change de valeur à chaque fois qu'on appuie sur le lien hypertexte de la page du tableau
        $paginatedVilles = $villeRepository->findPaginatedCities($page);

        // Afficher la page  index.html.twig + retourner notre liste 'villes'
        return $this->render('ville/index.html.twig', [
           'villes'=> $paginatedVilles

        ]);
    }

    /**
     * @Route("/{id}/modifier", name="ville_modifier", methods={"GET","POST"})
     */
    public function modifier(Request $request, Ville $ville): Response
    {
        // Création du formulaire de ville ( ville c
        $form = $this->createForm(VilleType::class, $ville);

        // Récupération des des données soumises par l'utilisateur et les injecter dans l'objet $ville
        $form->handleRequest($request);

        // Si formulaire rempli correctement l'objet $ville est injecté dans la BDD avec getDoctrine()->getManger()
        // + redirige vers la page de l'index des villes
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            // Message de succes affiché
            $this->addFlash('success', $ville->getNom() . " a bien été modifiée !");

            // On arajoute le paramètre 'page"=>1 dans l'URL : permet de donner l'information à la fonction findPaginatedCities()
            return $this->redirectToRoute('ville_liste',[
                'page'=>1
            ]);

        }


        // Redirection vers la page de l'index + retourne l'objet $ville récupérer dans la index
        // + création du formulaire affichant les données de la ville à modifier
        return $this->render('ville/modification.html.twig', [
            'ville' => $ville,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="ville_supprimer", methods={"POST"})
     */
    public function supprimer(Request $request, Ville $ville): Response
    {

        //si la ville est associée à des lieux, on ne peut pas la supprimer
        if ($ville->getLieus()->count()>0){
            $this->addFlash('warning', $ville->getNom() . " est associée à des lieux, et ne peut être supprimée !");

            return $this->redirectToRoute('ville_liste',[
                'page'=>1
            ]);
        }

        //if ($this->isCsrfTokenValid('delete'.$ville->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ville);
            $entityManager->flush();
            $this->addFlash('success','la ville a été supprimée');


        return $this->redirectToRoute('ville_liste',[
            'page'=>1
        ]);
    }

    /**
     * @Route("/afficher/{id}", name="ville_afficher", methods={"GET"})
     */
    public function afficher(Ville $ville): Response
    {
        // récupère l'ID de l'objet ville affiché sur la liste de la page index
        // et retourne un twig qui affichera les infos de la ville séléctionnée
        return $this->render('ville/affichage.html.twig', [
            'ville' => $ville,
        ]);
    }

    /**
     * @Route("/ajouter", name="ville_ajouter", methods={"GET","POST"})
     */
    public function ajouter(Request $request): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash('success', $ville->getNom() . " a bien été créée !");

            return $this->redirectToRoute('ville_liste',[
                'page'=>1
            ]);
        }

        return $this->render('ville/creation.html.twig', [
            'form' => $form->createView(),
            'ville' => $ville,
        ]);
    }


}
