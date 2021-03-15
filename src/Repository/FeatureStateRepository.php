<?php

namespace App\Repository;

use App\Entity\FeatureState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FeatureState|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeatureState|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeatureState[]    findAll()
 * @method FeatureState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeatureStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeatureState::class);
    }

    public function findInitialState()
    {
        $result = $this->createQueryBuilder('s')
            ->orderBy('s.position', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        return $result[0];
    }

    public function findLastState()
    {
        $result = $this->createQueryBuilder('s')
            ->orderBy('s.position', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        return $result[0];
    }


    // /**
    //  * @return FeatureState[] Returns an array of FeatureState objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FeatureState
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
