<?php

namespace App\Repository;

use App\Entity\Feature;
use App\Entity\FeatureTag;
use App\Entity\Feedback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Feature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feature[]    findAll()
 * @method Feature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feature::class);
    }

    public function findCompanyFeaturesByTag($tags, $company, $state)
    {

       $qb = $this->createQueryBuilder('f');
       $qb->leftJoin('f.tags', 't');

       if($tags)
       {
           $qb->where('t IN (:tags)');
           $qb->setParameter('tags', $tags);
       }

       if($state)
       {
           $qb->andWhere('f.state = :state');
           $qb->setParameter('state', $state);
       }

       $qb->andWhere('f.company = :company');
       $qb->orderBy('f.score', 'DESC');
       $qb->setParameter('company', $company);

       return $qb->getQuery()->getResult();
    }


    // /**
    //  * @return Feature[] Returns an array of Feature objects
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
    public function findOneBySomeField($value): ?Feature
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
