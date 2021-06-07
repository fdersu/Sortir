<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Ville;
use App\Form\LieuFormType;
use App\Form\SiteFormType;
use App\Form\VilleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{

    /**
     * @Route("/lieu_add", name="lieu_add")
     */
    public function lieu(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();

        $lieux = $entityManager->getRepository(Lieu::class)->findAll();

        $lieuForm = $this->createForm(LieuFormType::class, $lieu);
        $lieuForm->handleRequest($request);

        if($lieuForm->isSubmitted() && $lieuForm->isValid()){

            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu ajouté !');
            return $this->redirectToRoute('sortie_add');

        }


        return $this->render('sortie/lieu.html.twig', [
            'lieuForm' => $lieuForm->createView(),
            'lieux' => $lieux,
        ]);

    }


    /**
     * @Route ("/lieu_update/{lieu_id}", name="lieu_update", requirements={"lieu_id"="\d+"})
     */
    public function lieuUpdate(Request $request, EntityManagerInterface $entityManager, $lieu_id=null): Response
    {
        $lieu = $entityManager->getRepository(Lieu::class)->find($lieu_id);

        $lieux = $entityManager->getRepository(Lieu::class)->findAll();

        $lieuForm = $this->createForm(LieuFormType::class, $lieu);
        $lieuForm->handleRequest($request);

        if($lieuForm->isSubmitted() && $lieuForm->isValid()){

            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu modifié !');
            return $this->redirectToRoute('lieu_add');

        }


        return $this->render('sortie/lieu.html.twig', [
            'lieuForm' => $lieuForm->createView(),
            'lieux' => $lieux,
        ]);

    }


    /**
     * @Route("/lieu_delete/{lieu_id}", name="lieu_delete")
     */
    public function delete($lieu_id, EntityManagerInterface $entityManager): Response
    {
        $lieuToDelete = $entityManager->find(Lieu::class, $lieu_id);

        $entityManager->remove($lieuToDelete);
        $entityManager->flush();

        $this->addFlash('success', 'La ville a été supprimée');

        return $this->redirectToRoute('lieu_add');
    }
}
