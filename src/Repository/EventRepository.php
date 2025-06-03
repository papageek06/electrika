<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findByDistinct(): array
    {
        return $this->createQueryBuilder('e')
            ->select('MIN(e.id) , e.name ') // Prend le plus petit ID par événement
            ->groupBy('e.name') // Groupe par nom pour éviter les doublons
            ->orderBy('e.name', 'ASC') // Trie par ordre alphabétique
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findByEventDistinct(): array
    {
        return $this->createQueryBuilder('e')
             ->select(' e.name, ed.mouve  , ed.date')
             ->innerJoin('e.eventDetails', 'ed')
             ->distinct('ed.mouve')
            ->getQuery()
            ->getResult()
        ;
    }



    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
