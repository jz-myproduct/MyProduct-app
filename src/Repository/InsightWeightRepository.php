<?php

namespace App\Repository;

use App\Entity\InsightWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InsightWeight|null find($id, $lockMode = null, $lockVersion = null)
 * @method InsightWeight|null findOneBy(array $criteria, array $orderBy = null)
 * @method InsightWeight[]    findAll()
 * @method InsightWeight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InsightWeightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InsightWeight::class);
    }

    // /**
    //  * @return InsightValue[] Returns an array of InsightValue objects
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
    public function findOneBySomeField($value): ?InsightValue
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
