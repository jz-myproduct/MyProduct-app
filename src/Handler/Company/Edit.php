<?php


namespace App\Handler\Company;


use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;

class Edit
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(Company $company)
    {
        $this->manager->flush();
    }

}