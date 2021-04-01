<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof Company) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function getSimilarSlugsCountForExistingCompany(String $slug, Company $company): int
    {
        $count = $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.slug LIKE :slug')
            ->andWhere('c != :company')
            ->setParameter('slug', $slug.'%')
            ->setParameter('company', $company)
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$count;
    }

    public function getSimilarSlugsCountForNewCompany(String $slug)
    {
        $count = $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.slug LIKE :slug')
            ->setParameter('slug', $slug.'%')
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$count;
    }

    public function findCompanyByEmail(string $email)
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT c
                  FROM App\Entity\Company c
                  WHERE c.email = :query'
        )
            ->setParameter('query', $email)
            ->getOneOrNullResult();
    }
}
