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

    public function findCompanyFeaturesByTag($tags, $company, $state, $fulltext)
    {

       $qb = $this->createQueryBuilder('f');
       $qb->select('f, t, p');
       $qb->join('f.tags', 't');
       $qb->join('f.portalFeature', 'p');
       $qb->where('f.company = :company');
       $qb->setParameter('company', $company);

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

       $qb->orderBy('f.score', 'DESC');

       return $qb->getQuery()->getResult();
    }

    public function findUnsedFeaturesForFeedback(Company $company, $tags, $features, $fulltext = null)
    {

        $qb = $this->createQueryBuilder('f')
                   ->join('f.insights', 'i')
                   ->join('f.portalFeature', 'pf');

        if($tags)
        {
            $qb->join('f.tags', 'ta');
        }

        $qb->addSelect('pf')
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

        return $qb->getQuery()->getResult();
    }

    public function findUsedFeaturesForFeedback($feedback)
    {
        return $this->createQueryBuilder('f')
                     ->join('f.insights', 'i')
                     ->join('i.feedback', 'fee')
                     ->join('f.portalFeature', 'pf')
                     ->addSelect('pf')
                     ->where('fee = :feedback')
                     ->setParameter('feedback', $feedback)
                     ->getQuery()
                     ->getResult();
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
