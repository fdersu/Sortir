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

    /**
     * @Route("", name="home")
     */
    public function home(): Response
    {
        return $this->redirectToRoute("app_login");
    }

    /**
     * @Route("accueil", name="accueil")
     */
    public function accueil(SortieRepository $sortieRepository, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $now = new \DateTime();
        $allSorties = $sortieRepository->findWithinLastMonth();
        $filter = new Filter();
        $filterForm = $this->createForm(FilterType::class, $filter);
        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $allSorties = $sortieRepository->findByFilter($filter, $user);
            if (empty($allSorties)) {
                $this->addFlash('notice', "Aucun résultat pour votre recherche.");
            }
        }
        return $this->render('main/accueil_flxgrd.html.twig', [
            'filterForm' => $filterForm->createView(),
            'sorties' => $allSorties,
            'now' => $now
        ]);
    }

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
