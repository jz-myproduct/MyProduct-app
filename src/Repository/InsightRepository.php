<?php

namespace App\Repository;

use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function getInsightsCountForFeature(Feature $feature)
    {
        $count = $this->createQueryBuilder('i')
                      ->select('count(i.id)')
                      ->where('i.feature = :feature')
                      ->setParameter('feature', $feature)
                      ->getQuery()
                      ->getSingleScalarResult();

        return (int)$count;
    }

    public function getScoreCountForFeature(Feature $feature)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $sql = 'SELECT sum(w.number)
                FROM insight i
                JOIN insight_weight w 
                ON i.weight_id = w.id
                WHERE i.feature_id = :id
                    ';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'id' => $feature->getId()
        ]);
        return (int)$stmt->fetchOne();
    }

    public function getFeedbackCountForPortalFeature(Feature $feature)
    {

        $conn = $this->getEntityManager()
            ->getConnection();

        $sql = 'SELECT count(*)
                FROM insight i 
                JOIN feedback fb
                ON i.feedback_id = fb.id
                JOIN feature fe 
                ON i.feature_id = fe.id
                JOIN portal_feature p
                ON fe.id = p.feature_id
                WHERE i.feature_id = :id AND fb.from_portal = 1
                    ';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'id' => $feature->getId()
        ]);
        return (int)$stmt->fetchOne();

    }

    public function findInsightsForFeedback(Feedback $feedback)
    {
        return $this->createQueryBuilder('i')
            ->select('i, fea, fee, w, s')
            ->join('i.feature', 'fea')
            ->join('i.feedback', 'fee')
            ->leftJoin('i.weight', 'w')
            ->leftJoin('fea.state', 's')
            ->leftJoin('fea.tags', 'ta')
            ->where('fee = :feedback')
            ->setParameter('feedback', $feedback)
            ->orderBy('fea.score', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findInsightsForFeature(Feature $feature)
    {
        return $this->createQueryBuilder('i')
                    ->select('i, fea, fee, w')
                    ->join('i.feature', 'fea')
                    ->join('i.feedback', 'fee')
                    ->join('i.weight', 'w')
                    ->where('fea = :feature')
                    ->setParameter('feature', $feature)
                    ->orderBy('i.weight', 'DESC')
                    ->getQuery()
                    ->getResult();
    }
}
