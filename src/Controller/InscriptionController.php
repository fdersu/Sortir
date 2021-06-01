<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\User;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    /** @Route("/inscription/{sortie_id}", name="inscription_inscription", requirements={"sortie_id"="\d+"}) */
    public function inscription(EntityManagerInterface $entityManager,SortieRepository $sortieRepository, $sortie_id): Response
    {
        /** @var User $user*/
        $user = $this->getUser();
        $now = new \DateTime();
        $sortie = $sortieRepository->find($sortie_id);
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
            if($inscription->getParticipant() === $user){
                $entityManager->remove($inscription);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('main_accueil');
    }
}
