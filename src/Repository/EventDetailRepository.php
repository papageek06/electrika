<?php

namespace App\Repository;

use App\Entity\EventDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PDO;

/**
 * @extends ServiceEntityRepository<EventDetail>
 */
class EventDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventDetail::class);
    }

    //    /**
    //     * @return EventDetail[] Returns an array of EventDetail objects
    //     */
       public function findByName($value ): array
       {
           return $this->createQueryBuilder('ed')
               ->andWhere('ed.name LIKE :val l ')
               ->setParameter('val','%'.$value.'%')
               ->orderBy('ed.name', 'ASC')
            //    ->setMaxResults(10)
               ->getQuery()
               ->getResult()
           ;
       }



    //    public function findOneBySomeField($value): ?EventDetail
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

}
