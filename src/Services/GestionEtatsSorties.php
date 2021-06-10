<?php


namespace App\Services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;


class GestionEtatsSorties
{

    private $entityManager;
    private $etatRepository;

    public function __construct(EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {
        $this->etatRepository = $etatRepository;
        $this->entityManager = $entityManager;
    }


    public function setEtats(Sortie $sortie){
        /** @var \DateTime $dateDebut */
        $dateDebut = $sortie->getDateDebut();
        $dateFin = $dateDebut->modify('+'.$sortie->getDuree().' hour');
        $now = new \DateTime();

        //Définition de l'état en fonction des dates, sauf pour les sorties annulées
        if($sortie->getEtat()->getLibelle() != 'Annulée') {
            //Si la date de début est antérieure à aujourd'hui
            // et que la date de début ajoutée à la durée de l'activité n'est pas passée,
            //l'activité est en cours
            if ($dateDebut < $now && $now < $dateFin) {
                $sortie->setEtat($this->etatRepository->findOneBy(['libelle' => 'Activité en cours']));
                $this->entityManager->persist($sortie);
                $this->entityManager->flush();
            }
            //Si la date de début ajoutée à la durée de l'activité est pas passée,
            // l'activité est terminée
            if ($dateFin < $now) {
                $sortie->setEtat($this->etatRepository->findOneBy(['libelle' => 'Passée']));
                $this->entityManager->persist($sortie);
                $this->entityManager->flush();
            }
            //Si la date de cloture est passée mais pas la date de début,
            // l'activité est cloturée
            if ($sortie->getDateCloture() < $now && $now < $dateDebut) {
                $sortie->setEtat($this->etatRepository->findOneBy(['libelle' => 'Clôturée']));
                $this->entityManager->persist($sortie);
                $this->entityManager->flush();
            }
        }
    }
}