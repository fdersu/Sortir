<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Ville;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\LieuFormType;
use App\Form\SortieFormType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie")
     */
    public function index(): Response
    {
        return $this->render('sortie/home.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    /**
     * @Route("/sortie/detail/{id}", name="sortie_detail")
     */
    public function detail($id, Request $request, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie
        ]);
    }

    /**
     * @Route("/sortie_add", name="sortie_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {

        $sortie = new Sortie();
        $lieu = new Lieu();
        //$ville = new Ville();

        $sortieForm = $this->createForm(SortieFormType::class, $sortie);
        $lieuForm = $this->createForm(LieuFormType::class, $lieu);
        //$villeForm = $this->createForm(LieuFormType::class, $ville);


        //Methode pour setter Organisateur direct dans le controlleur :
        $sortie->setOrganisateur($entityManager->getRepository(User::class)->findOneBy(['pseudo' => $this->getUser()->getUsername()]));


        $sortieForm->handleRequest($request);
        $lieuForm->handleRequest($request);
        //$villeForm->handleRequest($request);


        if($sortieForm->isSubmitted() && $sortieForm->isValid()){

            if($lieuForm->isValid()){

                $sortie->setEtat($entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']));
                //$entityManager->persist($ville);
                $entityManager->persist($lieu);
                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'Sortie ajoutée !');
                return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);

            }

        }


        return $this->render('sortie/add.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'lieuForm' => $lieuForm->createView(),
            //'villeForm' => $villeForm->createView()
        ]);

    }

    /**
     * @Route("/sortie_lieu", name="sortie_lieu")
     */
    public function lieu(Request $request, EntityManagerInterface $entityManager): Response
    {

        $lieu = new Lieu();
        //$ville = new Ville();

        $lieuForm = $this->createForm(LieuFormType::class, $lieu);
        //$villeForm = $this->createForm(LieuFormType::class, $ville);

        $lieuForm->handleRequest($request);
        //$villeForm->handleRequest($request);


        if($lieuForm->isSubmitted() && $lieuForm->isValid()){

                //$entityManager->persist($ville);
                $entityManager->persist($lieu);
                $entityManager->flush();

                $this->addFlash('success', 'Lieu ajouté !');
                return $this->redirectToRoute('sortie_add');

        }


        return $this->render('sortie/lieu.html.twig', [
            'lieuForm' => $lieuForm->createView(),
            //'villeForm' => $villeForm->createView()
        ]);

    }
}
