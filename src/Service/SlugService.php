<?php
namespace App\Service;


use App\Entity\Company;
use App\Entity\Portal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugService
{

    private $entityManager;
    /**
     * @var SluggerInterface
     */
    private $slugger;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    public function createCommonSlug(String $value): String
    {
        return $this->prepareSlug($value);
    }

    public function createInitialCompanySlug(String $value)
    {
        $slug = $this->prepareSlug($value);

        $initialCount = $this->entityManager->getRepository(Company::class)
            ->getSimilarSlugsCountForNewCompany($slug);

        $count = $this->checkUnique($slug, $initialCount, Company::class);

        return $this->handleCount($slug, $count);
    }

    public function createCompanySlug(String $value, Company $company)
    {
        $slug = $this->prepareSlug($value);

        $initialCount = $this->entityManager->getRepository(Company::class)
            ->getSimilarSlugsCountForExistingCompany($slug, $company);

        $count = $this->checkUnique($slug, $initialCount, Company::class);

        return $this->handleCount($slug, $count);

    }

    public function createInitialPortalSlug(String $value)
    {
        $slug = $this->prepareSlug($value);

        $initialCount = $this->entityManager->getRepository(Portal::class)
            ->getSimilarSlugsCountForNewPortal($slug);

        $count = $this->checkUnique($slug, $initialCount, Portal::class);

        return $this->handleCount($slug, $count);
    }

    public function createPortalSlug(Portal $portal)
    {
        $slug = $this->prepareSlug(
            $portal->getName()
        );

        $initialCount = $this->entityManager->getRepository(Portal::class)
            ->getSimilarSlugsCountForExistingCompany($slug, $portal);

        $count = $this->checkUnique($slug, $initialCount, Portal::class);

        return $this->handleCount($slug, $count);
    }

    private function checkUnique($slug, $count, string $class)
    {
        $unique = false;
        while(!$unique)
        {
            if($this->entityManager->getRepository($class)->findBy(
                ['slug' => $this->handleCount($slug, $count)]
            ))
            {
                $count++;
                continue;
            }

            $unique = true;
        }

        return $count;
    }

    private function handleCount( $slug, $count )
    {
        if($count === 0){
            return $slug;
        }

        return $this->appendNumber( $slug, $count );
    }

    private function appendNumber(String $slug, int $count): String
    {
        return $slug.'-'.$count;
    }

    private function prepareSlug(String $value): String
    {
        return $this->slugger->slug($value, '-')->folded()->toString();
    }

}