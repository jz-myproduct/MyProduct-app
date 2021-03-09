<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Insight|null find($id, $lockMode = null, $lockVersion = null)
 * @method Insight|null findOneBy(array $criteria, array $orderBy = null)
 * @method Insight[]    findAll()
 * @method Insight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InsightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Insight::class);
    }

    public function getUnUsedFeaturesForFeedback(Feedback $feedback, Company $company)
    {
        $entityManager = $this->getEntityManager();

        $sql = 'SELECT f.id, f.name
                FROM feature f
                WHERE 
                f.company_id = ? AND
                f.id NOT IN (SELECT feature_id
                             FROM insight
                             WHERE feedback_id = ?); ';

        $rsm = new ResultSetMappingBuilder( $entityManager );
        $rsm->addRootEntityFromClassMetadata('App\Entity\Feature', 'f');

        return $entityManager
            ->createNativeQuery($sql, $rsm)
            ->setParameter(1, $company)
            ->setParameter(2, $feedback)
            ->getResult();

    }

    public function getInsightsCountForFeedback(Feedback $feedback)
    {
       $count = $this->createQueryBuilder('i')
                     ->select('count(i.id)')
                     ->where('i.feedback = :feedback')
                     ->setParameter('feedback', $feedback)
                     ->getQuery()
                     ->getSingleScalarResult();

       return (int)$count;
    }



    // /**
    //  * @return Insight[] Returns an array of Insight objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Insight
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
