<?php

namespace App\Repository;

use App\Entity\Company;
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

    public function findByName(String $string)
    {
        return $this->createQueryBuilder('f')
             ->select('f.id, f.name')
             ->where('f.name LIKE :string')
             ->orWhere('f.description LIKE :string')
             ->setParameter('string', '%'.$string.'%')
             ->getQuery()
             ->getResult();
    }

    public function findCompanyFeaturesByTagAndState($tags, $company, $state, $fulltext)
    {

       $qb = $this->createQueryBuilder('f')
                  ->select('f, p, t')
                  ->leftJoin('f.tags', 't')
                  ->leftjoin('f.portalFeature', 'p')
                  ->where('f.company = :company')
                  ->setParameter('company', $company);

       if($tags)
       {
           $qb->andWhere('t IN (:tags)');
           $qb->setParameter('tags', $tags);
       }

       if($state)
       {
           $qb->andWhere('f.state = :state');
           $qb->setParameter('state', $state);
       }

       if($fulltext)
       {
           $qb->andWhere('f.name LIKE :fulltext OR f.description LIKE :fulltext');
           $qb->setParameter('fulltext', '%'.$fulltext.'%');
       }

       $qb->addOrderBy('f.score', 'DESC')
          ->addOrderBy('f.updatedAt', 'DESC');

       return $qb->getQuery()->getResult();
    }

    public function findUnsedFeaturesForFeedback(Company $company, $tags, $features, $state, $fulltext = null)
    {

        $qb = $this->createQueryBuilder('f')
                   ->leftJoin('f.portalFeature', 'pf')
                   ->leftJoin('f.tags', 'ta')
                   ->leftJoin('f.state', 'st')
                   ->addSelect('pf, st')
                   ->where('f.company = :company')
                   ->setParameter('company', $company);


        if($fulltext)
        {
            $qb->andWhere('f.name LIKE :fulltext OR f.description LIKE :fulltext')
               ->setParameter('fulltext', '%'.$fulltext.'%');
        }

        if($features)
        {
            $qb->andWhere('f NOT IN (:features)')
                ->setParameter('features', $features);
        }

        if($tags)
        {
            $qb->andWhere('ta IN (:tags)')
               ->setParameter('tags', $tags);
        }

        if($state)
        {
            $qb->andWhere('st = :state')
               ->setParameter('state', $state);
        }

        $qb->addOrderBy('f.state', 'ASC')
           ->addOrderBy('f.score', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findUsedFeaturesForFeedback($feedback)
    {
        return $this->createQueryBuilder('f')
                     ->leftJoin('f.insights', 'i')
                     ->leftJoin('i.feedback', 'fee')
                     ->leftJoin('f.portalFeature', 'pf')
                     ->addSelect('pf')
                     ->where('fee = :feedback')
                     ->setParameter('feedback', $feedback)
                     ->getQuery()
                     ->getResult();
    }
}
