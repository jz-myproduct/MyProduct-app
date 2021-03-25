<?php

namespace App\Repository;

use App\Entity\PortalFeatureState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PortalFeatureState|null find($id, $lockMode = null, $lockVersion = null)
 * @method PortalFeatureState|null findOneBy(array $criteria, array $orderBy = null)
 * @method PortalFeatureState[]    findAll()
 * @method PortalFeatureState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortalFeatureStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PortalFeatureState::class);
    }

    public function findInitialState()
    {
        $result = $this->createQueryBuilder('s')
            ->orderBy('s.position', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        return $result[0];
    }
}
