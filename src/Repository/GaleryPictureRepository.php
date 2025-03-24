<?php

namespace App\Repository;

use App\Entity\GaleryPicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GaleryPicture>
 */
class GaleryPictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GaleryPicture::class);
    }

    //    /**
    //     * @return GaleryPicture[] Returns an array of GaleryPicture objects
    //     */
       public function findByEvent(): array
       {
           return $this->createQueryBuilder('g')

               ->innerJoin('g.event', 'e')
               ->getQuery()
               ->getResult()
           ;
       }

       public function findOneBysite($value): ?GaleryPicture
       {
           return $this->createQueryBuilder('g')
               ->andWhere('g.site = :val')
               ->setParameter('val', $value)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }
}
