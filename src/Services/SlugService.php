<?php
namespace App\Services;


use App\Entity\Company;
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

    public function createCompanySlug(String $value): String
    {
        $slug = self::prepareSlug($value);

        $count = $this->entityManager->getRepository(Company::class)->getSimilarSlugsCount($slug);
        if($count === 0){
            return self::prepareSlug($value);
        }

        return self::appendNumber( $slug, $count);
    }


    public function createGeneralSlug(String $value): String
    {
        return $this->prepareSlug($value);
    }

    private static function appendNumber(String $slug, int $count): String
    {
        return $slug.'-'.$count;
    }

    private static function prepareSlug(String $value): String
    {

        /* remove non-alphanumeric */
        $slug = preg_replace("/[^A-Za-z0-9 ]/", '', $value);
        /* to lowercase and replace ' ' with - */
        $slug = strtolower(str_replace(" ", "-", $slug));

        return $slug;
    }

}