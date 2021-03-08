<?php

namespace App\Repository;

use App\Entity\FeedbackValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FeedbackValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeedbackValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeedbackValue[]    findAll()
 * @method FeedbackValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedbackValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedbackValue::class);
    }

    // /**
    //  * @return FeedbackValue[] Returns an array of FeedbackValue objects
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
    public function findOneBySomeField($value): ?FeedbackValue
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
