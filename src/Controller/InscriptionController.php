<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\User;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InscriptionController
 * @package App\Controller
 * Inscription et désistement à une sortie
 */
class InscriptionController extends AbstractController
{

    //Fonction incription pour s'inscrire en tant que participant à une sortie
    /** @Route("/inscription/{sortie_id}", name="inscription_inscription", requirements={"sortie_id"="\d+"}) */
    public function inscription(EntityManagerInterface $entityManager,SortieRepository $sortieRepository, $sortie_id): Response
    {
        /** @var User $user*/
        $user = $this->getUser();
        $now = new \DateTime();

        //Selectionnée une sortie par son ID
        $sortie = $sortieRepository->find($sortie_id);

        //Si le nombre d'inscription maximum n'est pas dépassé, et que la sortie  n'est pas cloturée
        if($sortie->getInscriptions()->count() < $sortie->getNbInscriptionsMax() && $sortie->getDateCloture() > $now) {
            $inscription = new Inscription();
            $inscription->setSortie($sortie)
                ->setParticipant($user)
                ->setDateInscription($now);
            $entityManager->persist($inscription);
            $entityManager->flush();
        }
        return $this->redirectToRoute('main_accueil');
    }

    /** @Route("/desistement/{sortie_id}", name="inscription_desistement", requirements={"sortie_id"="\d+"}) */
    public function desistement(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, $sortie_id){
        /** @var User $user */
        $user = $this->getUser();
        $sortie = $sortieRepository->find($sortie_id);
        foreach ($sortie->getInscriptions() as $inscription){

            //Si l'id du user en session est identique a un id inscrit à une sortie
            if($inscription->getParticipant() === $user){
                $entityManager->remove($inscription);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('main_accueil');
    }
}
