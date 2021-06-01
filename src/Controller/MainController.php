<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\FilterType;
use App\Form\Model\Filter;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this -> redirectToRoute("app_login");
    }

    /**
     * @Route("accueil", name="accueil")
     */
    public function accueil(SortieRepository $sortieRepository, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $allSorties = $sortieRepository->findBy([], ['dateDebut' => 'DESC']);
        $filter = new Filter();
        $filterForm = $this->createForm(FilterType::class, $filter);
        $filterForm->handleRequest($request);
        if($filterForm->isSubmitted() && $filterForm->isValid()){
            $allSorties = $sortieRepository->findByFilter($filter, $user);
        }
        return $this->render('main/accueil.html.twig', [
            'filterForm' => $filterForm->createView(),
            'sorties' => $allSorties
        ]);
    }
}
