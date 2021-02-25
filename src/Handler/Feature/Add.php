<?php


namespace App\Handler\Feature;


use App\Entity\Company;
use App\Entity\Feature;
use Doctrine\ORM\EntityManagerInterface;

class Add
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(Feature $feature, Company $company)
    {
        $feature->setCompany($company);
        $feature->setInitialScore();

        $currentDateTime = new \DateTime();
        $feature->setCreatedAt($currentDateTime);
        $feature->setUpdatedAt($currentDateTime);

        $this->manager->persist($feature);
        $this->manager->flush();
    }

}