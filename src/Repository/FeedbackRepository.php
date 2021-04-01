<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Feedback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findForFilteredList(Company $company, string $fulltext = null, bool $isNew = null)
    {
        $qb =
             $this->createQueryBuilder('f')
                  ->where('f.company = :company')
                  ->setParameter('company', $company);

        if($fulltext)
        {
            $qb->andWhere('f.description LIKE :fulltext OR f.source LIKE :fulltext')
               ->setParameter('fulltext', '%'.$fulltext.'%');
        }

        if(!is_null($isNew))
        {
            $qb->andWhere('f.isNew = :isNew')
               ->setParameter('isNew', $isNew);
        }

        return $qb
                ->addOrderBy('f.isNew', 'DESC')
                ->addOrderBy('f.updatedAt', 'DESC')
                ->getQuery()
                ->getResult();
    }
}
