<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\FilterType;
use App\Form\Model\Filter;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MainController
 * @Route ("/", name="main_")
 */
class MainController extends AbstractController
{
    /** Redirection vers la page de login */
    /**
     * @Route("", name="home")
     */
    public function home(): Response
    {
        return $this->redirectToRoute("app_login");
    }

    /** Méthode d'affichage de la page d'accueil */
    /**
     * @Route("accueil", name="accueil")
     */
    public function accueil(SortieRepository $sortieRepository, Request $request): Response
    {
        //Instance de user
        /** @var User $user */
        $user = $this->getUser();

        //Variable de la date actuelle
        $now = new \DateTime();

        //Toutes les sorties datant de moins d'un mois
        $allSorties = $sortieRepository->findWithinLastMonth();

        $filter = new Filter();

        //Création du formulaire de filtre
        $filterForm = $this->createForm(FilterType::class, $filter);

        //Récuperer les informations du POST
        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $allSorties = $sortieRepository->findByFilter($filter, $user);

            //Si il n'y a aucune sorties correspondant aux filtres
            if (empty($allSorties)) {
                $this->addFlash('notice', "Aucun résultat pour votre recherche.");
            }
        }

        //Rafraichir la page + affichage des sorties en fonction des filtres
        return $this->render('main/accueil.html.twig', [
            'filterForm' => $filterForm->createView(),
            'sorties' => $allSorties,
            'now' => $now
        ]);
    }

    /** Traitement de requête AJAX pour l'affichage du motif d'annulation d'une sortie */
    /** @Route("/accueil/ajax/motif", name="main_ajax_motif") */
    public function ajaxMotif(Request $request, SortieRepository $sortieRepository)
    {
        $sortie = new Sortie();
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent());
            $sortie = $sortieRepository->find($data->id);
        }
        return new JsonResponse(['motif' => $sortie->getMotifAnnulation()]);
    }
}
