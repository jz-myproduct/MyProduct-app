<?php

namespace App\Repository;

use App\Entity\Portal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Portal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Portal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Portal[]    findAll()
 * @method Portal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Portal::class);
    }

    public function getSimilarSlugsCountForExistingCompany(String $slug, Portal $portal)
    {

        $count = $this->createQueryBuilder('p')
                      ->select('count(p.id)')
                      ->where('p.slug LIKE :slug')
                      ->andWhere('p != :portal')
                      ->setParameter('slug', $slug.'%')
                      ->setParameter('portal', $portal)
                      ->getQuery()
                      ->getSingleScalarResult();

        return (int)$count;
    }

    public function getSimilarSlugsCountForNewPortal(String $slug)
    {
        $count = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.slug LIKE :slug')
            ->setParameter('slug', $slug.'%')
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$count;
    }
}
