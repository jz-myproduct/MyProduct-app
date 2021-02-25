<?php
namespace App\Services;


use App\Entity\Company;
use App\Entity\Portal;
use phpDocumentor\Reflection\Types\ClassString;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Scalar\String_;
use Doctrine\ORM\EntityManagerInterface;

class SlugService
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createCommonSlug(String $value): String
    {
        return $this->prepareSlug($value);
    }

    public function createCompanySlug(String $value)
    {
        $slug = $this->prepareSlug($value);

        $count = $this->entityManager->getRepository(Company::class)
            ->getSimilarSlugsCount($slug);

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
        /* remove non-alphanumeric */
        $slug = preg_replace("/[^A-Za-z0-9 ]/", '', $value);
        /* to lowercase and replace ' ' with - */
        $slug = strtolower(str_replace(" ", "-", $slug));

        return $slug;
    }


}