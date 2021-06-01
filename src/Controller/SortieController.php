<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie_sortie")
     */
    public function detail(): Response
    {

    }

    /**
     * @Route("/sortie_add", name="sortie_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {

        $sortie = new Sortie();

        $sortieForm = $this->createForm(SortieController::class, $sortie);


        //Methode pour setter Organisateur direct dans le controlleur :
        $sortie->setOrganisateur($entityManager->getRepository(Etat::class)->findOneBy(['pseudo' => $this->getUser()->getUsername()]));


        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()){

            $sortie->setEtat($entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']));
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie ajoutée !');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);

        }

        return $this->render('sortie/add.html.twig', [
            'wishForm' => $sortieForm->createView()
        ]);

    }

}
