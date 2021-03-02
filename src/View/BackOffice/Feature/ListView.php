<?php


namespace App\View\BackOffice\Feature;


use App\Entity\Company;
use App\Entity\Feature;
use Doctrine\ORM\EntityManagerInterface;

class ListView
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(Company $company)
    {
        $featureList =  $this->manager->getRepository(Feature::class)
            ->findBy(['company' => $company], ['score' => 'DESC']);

        return [
            'featureList' => $featureList
        ];
    }

}