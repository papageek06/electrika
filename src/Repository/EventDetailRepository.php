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
       public function countOrdersByStatus(): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.mouve AS status, COUNT(e.id) AS total')
                    ->where('e.date > :today')
        ->setParameter('today', new \DateTimeImmutable('today'))
            ->groupBy('e.mouve')
            ->getQuery()
            ->getResult();
    }

    public function countstockByProduct(): array
    {
        return $this->createQueryBuilder('e')
        ->select('IDENTITY(e.product) as product_id, SUM(e.quantity) as Tquantity')
        ->where('e.mouve = :bl')
        ->setParameter('bl', 'bl') 
        ->groupBy('e.product')
        ->getQuery()
        ->getResult();
    }

    public function findByEventDetailDistinct(): array
    {
        return $this->createQueryBuilder('ed')
            ->select('e.name, ed.mouve, ed.date as date, e.id as eventId, ed.id as eventDetailId')
            ->join('ed.event', 'e')
            ->groupBy('e.id, ed.mouve, ed.date, ed.id')
            ->getQuery()
            ->getResult();
    }
public function findProductsAndQuantitiesByEventAndMouve($event, string $mouve): array
{
    return $this->createQueryBuilder('ed')
         ->select('p.id AS id, p.name AS name, p.picture AS picture, SUM(ed.quantity) AS total_quantity')
        ->join('ed.product', 'p')
        ->where('ed.event = :event')
        ->andWhere('ed.mouve = :mouve')
        ->groupBy('p')
        ->setParameter('event', $event)
        ->setParameter('mouve', $mouve)
        ->getQuery()
        ->getResult();
}




    



}
