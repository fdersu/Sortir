<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\Model\Filter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /** @return Sortie[] Returns an array of Sortie objects */
    public function findWithinLastMonth(){
        $now = new \DateTime();
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateDebut > :val')
            ->setParameter('val', $now->modify('-1 month'))
            ->orderBy('s.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Sortie[] Returns an array of Sortie objects */
    public function findByFilter(Filter $filter, User $user)
    {
        if ($filter) {
            $qb = $this->createQueryBuilder('s')
                ->leftJoin('s.inscriptions', 'i')
                ->addSelect('i');
            if ($filter->getSite()) {
                $qb->andWhere('s.site = :val')
                    ->setParameter('val', $filter->getSite());
            }
            if ($filter->getRecherche()) {
                $qb->andWhere('s.nom LIKE :val1')
                    ->setParameter('val1', '%' . $filter->getRecherche() . '%');
            }
            if ($filter->getDateDebut()) {
                $qb->andWhere('s.dateDebut >= :val2')
                    ->setParameter('val2', $filter->getDateDebut());
            }
            if ($filter->getDateFin()) {
                $qb->andWhere('s.dateDebut <= :val3')
                    ->setParameter('val3', $filter->getDateFin());
            }
            if ($filter->getOrganisateur()) {
                $qb->andWhere('s.organisateur = :val4')
                    ->setParameter('val4', $user);
            }
            if ($filter->getInscrit()) {
                $qb->andWhere('i.participant = :val5')
                    ->setParameter('val5', $user);
            }
            if ($filter->getNonInscrit()) {
                $qb->andWhere('i.participant != :val6')
                    ->setParameter('val6', $user);
            }
            if ($filter->getSortiesPassees()) {
                $qb->andWhere('s.dateDebut < :val7')
                    ->setParameter('val7', new \DateTime());
            }
            $dateLimite = new \DateTime();
            return $qb->andWhere('s.dateDebut > :val8')
                ->setParameter('val8', $dateLimite->modify('-1 month'))
                ->orderBy('s.dateDebut', 'ASC')
                ->getQuery()
                ->getResult();
        }
        return $this->findBy([], ['dateDebut' => 'ASC']);
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
