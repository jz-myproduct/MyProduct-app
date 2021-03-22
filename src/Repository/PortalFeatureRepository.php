<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\PortalFeature;
use App\Entity\PortalFeatureState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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
            ->getQuery()
            ->getResult();

    }






    // /**
    //  * @return PortalFeature[] Returns an array of PortalFeature objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PortalFeature
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
