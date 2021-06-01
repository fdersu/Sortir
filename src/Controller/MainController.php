<?php

namespace App\Controller;

use App\Form\FilterType;
use App\Form\Model\Filter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MainController
 * @Route ("/", name="main_")
 */
class MainController extends AbstractController
{

    /**
     * @Route("/home", name="home")
     */
    public function home(): Response
    {
        return $this -> render("main/home.html.twig");
    }

    /**
     * @Route("index", name="index")
     */
    public function index(): Response
    {
        $filter = new Filter();
        $filterForm = $this->createForm(FilterType::class, $filter);
        return $this->render('main/index.html.twig', [
            'filterForm' => $filterForm->createView(),
        ]);
    }
}
