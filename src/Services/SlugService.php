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

    public function createGeneralSlug(String $value): String
    {
        return $this->prepareSlug($value);
    }

    public function createCompanySlug(String $value)
    {
        return $this->handleUniqueSlug($value, Company::class);
    }

    public function createPortalSlug(String $value)
    {
        return $this->handleUniqueSlug($value, Portal::class);
    }

    private function handleUniqueSlug(String $value, String $class)
    {
        $slug = $this->prepareSlug($value);

        $count = $this->entityManager->getRepository($class)->getSimilarSlugsCount($slug);

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