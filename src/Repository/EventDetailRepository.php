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
               ->andWhere('ed.name LIKE :val ')
               ->setParameter('val','%'.$value.'%')
               ->orderBy('ed.name', 'ASC')
            //    ->setMaxResults(10)
               ->getQuery()
               ->getResult()
           ;
       }



       public function findByStatus(string $status , $order): array
       {
           return $this->createQueryBuilder('e')
               ->andWhere('e.mouve = :status')
               ->setParameter('status', $status)
               ->orderBy('e.date' , $order )
               ->getQuery()
               ->getResult()
           ;
       }

       public function findByEvent( $event , $order): array
       {
           return $this->createQueryBuilder('e')
               ->andWhere('e.event = :event')
               ->setParameter('event', $event )
               ->orderBy('e.date' , $order )
               ->getQuery()
               ->getResult()
           ;
       }

       public function findByEventStatus( $event , $status , $order): array
       {
           return $this->createQueryBuilder('e')
               ->andWhere('e.id = :event')
               ->andWhere('e.mouve = :status')
               ->setParameter('event',$event)
               ->setParameter('status',$status)
               ->orderBy('e.date' , $order )
               ->getQuery()
               ->getResult()
           ;
       }

}
