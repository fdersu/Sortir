<?php

namespace App\Controller;

use App\Form\FilterType;
use App\Form\Model\Filter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/index", name="main_index")
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
