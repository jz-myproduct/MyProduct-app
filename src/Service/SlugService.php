<?php
namespace App\Service;


use App\Entity\Company;
use App\Entity\Portal;
use phpDocumentor\Reflection\Types\ClassString;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Scalar\String_;
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

        $count = $this->entityManager->getRepository(Company::class)
            ->getSimilarSlugsCountForNewCompany($slug);

        return $this->handleCount($slug, $count);
    }

    public function createCompanySlug(String $value, Company $company)
    {
        $slug = $this->prepareSlug($value);

        $count = $this->entityManager->getRepository(Company::class)
            ->getSimilarSlugsCountForExistingCompany($slug, $company);

        return $this->handleCount($slug, $count);

    }

    public function createInitialPortalSlug(String $value)
    {
        $slug = $this->prepareSlug($value);

        $count = $this->entityManager->getRepository(Portal::class)
            ->getSimilarSlugsCountForNewPortal($slug);

        return $this->handleCount($slug, $count);
    }

    public function createPortalSlug(Portal $portal)
    {
        $slug = $this->prepareSlug(
            $portal->getName()
        );

        $count = $this->entityManager->getRepository(Portal::class)
            ->getSimilarSlugsCountForExistingCompany($slug, $portal);

        return $this->handleCount($slug, $count);
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