<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Portal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT count(id)
                FROM portal
                WHERE slug LIKE ? AND id != ?";

        // TODO vylepšit exception
        $stmt = $conn->prepare($sql);
        $stmt->execute([$slug . '%', $portal->getId()]);

        return (int)$stmt->fetchOne();
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

    // /**
    //  * @return Portal[] Returns an array of Portal objects
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
    public function findOneBySomeField($value): ?Portal
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
