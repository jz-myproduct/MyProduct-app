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
        return $this->createQueryBuilder('s')
                    ->orderBy('s.position', 'ASC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    public function findLastState()
    {
        return $this->createQueryBuilder('s')
                    ->orderBy('s.position', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();

    }
}
