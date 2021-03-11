<?php


namespace App\Handler\Feedback;


use App\Entity\Company;
use App\Entity\Feedback;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class AddFromPortal
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(Feedback $feedback, Company $company)
    {
        $feedback->setCompany($company);
        $feedback->setIsNew(true);
        $feedback->setFromPortal(true);

        $currentDateTime = new \DateTime();
        $feedback->setCreatedAt($currentDateTime);
        $feedback->setUpdatedAt($currentDateTime);

        $this->manager->persist($feedback);
        $this->manager->flush();

        return $feedback;
    }

}