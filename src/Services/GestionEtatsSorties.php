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

        if($sortie->getEtat()->getLibelle() != 'Annulée') {
            if ($dateDebut < $now && $now < $dateFin) {
                $sortie->setEtat($this->etatRepository->findOneBy(['libelle' => 'Activité en cours']));
                $this->entityManager->persist($sortie);
                $this->entityManager->flush();
            }
            if ($dateFin < $now) {
                $sortie->setEtat($this->etatRepository->findOneBy(['libelle' => 'Passée']));
                $this->entityManager->persist($sortie);
                $this->entityManager->flush();
            }
            if ($sortie->getDateCloture() < $now && $now < $dateDebut) {
                $sortie->setEtat($this->etatRepository->findOneBy(['libelle' => 'Clôturée']));
                $this->entityManager->persist($sortie);
                $this->entityManager->flush();
            }
        }
    }
}