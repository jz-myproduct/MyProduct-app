<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Feedback|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feedback|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feedback[]    findAll()
 * @method Feedback[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedbackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedback::class);
    }

    public function getFeedbackCountForFeature(Feature $feature)
    {
        $count = $this->createQueryBuilder('fe')
                    ->select('count(fe)')
                    ->innerJoin('fe.feature', 'fea')
                    ->where('fea.id = :feature_id')
                    ->setParameter('feature_id',$feature->getId())
                    ->getQuery()
                    ->getSingleScalarResult();

        return (int)$count;
    }

    public function getFeatureFeedback(Feature $feature)
    {
        return $this->createQueryBuilder('fe')
            ->select('fe')
            ->innerJoin('fe.feature', 'fea')
            ->where('fea.id = :feature_id')
            ->setParameter('feature_id',$feature->getId())
            ->getQuery()
            ->getResult();
    }

    public function getUnUsedFeaturesForFeedback(Feedback $feedback, Company $company)
    {
        $entityManager = $this->getEntityManager();

        $sql = 'SELECT f.id, f.name
                FROM feature f
                WHERE 
                f.company_id = ? AND
                f.id NOT IN (SELECT feature_id
                             FROM feedback_feature
                             WHERE feedback_id = ?); ';

        $rsm = new ResultSetMappingBuilder( $entityManager );
        $rsm->addRootEntityFromClassMetadata('App\Entity\Feature', 'f');

        return $entityManager
            ->createNativeQuery($sql, $rsm)
            ->setParameter(1, $company)
            ->setParameter(2, $feedback)
            ->getResult();

    }


    // /**
    //  * @return Feedback[] Returns an array of Feedback objects
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
    public function findOneBySomeField($value): ?Feedback
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
