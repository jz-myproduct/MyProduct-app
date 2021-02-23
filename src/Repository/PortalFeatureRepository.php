<?php

namespace App\Repository;

use App\Entity\PortalFeature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PortalFeature|null find($id, $lockMode = null, $lockVersion = null)
 * @method PortalFeature|null findOneBy(array $criteria, array $orderBy = null)
 * @method PortalFeature[]    findAll()
 * @method PortalFeature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortalFeatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PortalFeature::class);
    }

    // /**
    //  * @return PortalFeature[] Returns an array of PortalFeature objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PortalFeature
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
