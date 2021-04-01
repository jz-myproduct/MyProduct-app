<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\PortalFeature;
use App\Entity\PortalFeatureState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PortalFeature|null find($id, $lockMode = null, $lockVersion = null)
 * @method PortalFeature|null findOneBy(array $criteria, array $orderBy = null)
 * @method PortalFeature[]    findAll()
 * @method PortalFeature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortalFeatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PortalFeature::class);
    }

    public function findFeaturesForPortalByState(Company $company, PortalFeatureState $state)
    {

        return $this->createQueryBuilder('p')
            ->select('p, i, f')
            ->leftJoin('p.image', 'i')
            ->join('p.feature', 'f')
            ->where('f.company = :company')
            ->andWhere('p.display = 1')
            ->andWhere('p.state = :state')
            ->setParameter('company', $company)
            ->setParameter('state', $state)
            ->orderBy('p.feedbackCount', 'DESC')
            ->getQuery()
            ->getResult();

    }
}
