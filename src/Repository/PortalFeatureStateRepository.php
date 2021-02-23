<?php

namespace App\Repository;

use App\Entity\PortalFeatureState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PortalFeatureState|null find($id, $lockMode = null, $lockVersion = null)
 * @method PortalFeatureState|null findOneBy(array $criteria, array $orderBy = null)
 * @method PortalFeatureState[]    findAll()
 * @method PortalFeatureState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortalFeatureStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PortalFeatureState::class);
    }

    // /**
    //  * @return PortalFeatureState[] Returns an array of PortalFeatureState objects
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
    public function findOneBySomeField($value): ?PortalFeatureState
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
