<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\VilleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{

    /**
     * @Route("/ville_add", name="ville_add")
     */
    public function ville(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ville = new Ville();
        
        $villes = $entityManager->getRepository(Ville::class)->findAll();

        $villeForm = $this->createForm(VilleFormType::class, $ville);
        $villeForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()){

            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash('success', 'Ville ajoutée !');
            return $this->redirectToRoute('lieu_add');

        }


        return $this->render('sortie/ville.html.twig', [
            'villeForm' => $villeForm->createView(),
            'villes' => $villes,
        ]);

    }

    /**
     * @Route ("/ville_update/{ville_id}", name="ville_update", requirements={"ville_id"="\d+"})
     */
    public function villeUpdate(Request $request, EntityManagerInterface $entityManager, $ville_id=null): Response
    {
        $ville = $entityManager->getRepository(Ville::class)->find($ville_id);

        $villes = $entityManager->getRepository(Ville::class)->findAll();

        $villeForm = $this->createForm(VilleFormType::class, $ville);
        $villeForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()){

            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash('success', 'Ville modifiée !');
            return $this->redirectToRoute('ville_add');

        }


        return $this->render('sortie/ville.html.twig', [
            'villeForm' => $villeForm->createView(),
            'villes' => $villes,
        ]);

    }


    /**
     * @Route("/ville_delete/{ville_id}", name="ville_delete")
     */
    public function delete($ville_id, EntityManagerInterface $entityManager): Response
    {
        $villeToDelete = $entityManager->find(Ville::class, $ville_id);
        $linkedLieux = $entityManager->getRepository(Lieu::class)->findBy(['ville' => $villeToDelete]);

        if($linkedLieux){
            foreach ($linkedLieux as $linkedLieu) {
                $linkedSorties = $entityManager->getRepository(Sortie::class)->findBy(['lieu' => $linkedLieu]);

                if ($linkedSorties) {

                    //Suppression du lieu refusée si des sorties sont associées
                    $this->addFlash('error', 'Le lieu associé à cette ville ne peut pas être supprimé');

                    //Rechargement de la page, le reste du code n'est pas executé
                    return $this->redirectToRoute('ville_add');

                } else {
                    $entityManager->remove($linkedLieu);
                }
                //Si tous les lieux associés ont été supprimés, on supprime la ville puis recharge la page
                $entityManager->remove($villeToDelete);
                $entityManager->flush();

                $this->addFlash('success', 'La ville a été supprimée');

                return $this->redirectToRoute('ville_add');

            }
        }

    }


}
