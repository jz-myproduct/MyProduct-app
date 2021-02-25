<?php


namespace App\Handler\Feedback;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;

class AddOnFeatureDetail
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(Feedback $feedback, Company $company, Feature $feature)
    {
        $feedback->setCompany($company);
        $feedback->setIsNew(true);
        $feedback->setFromPortal(false);

        $currentDateTime = new \DateTime();
        $feedback->setCreatedAt($currentDateTime);
        $feedback->setUpdatedAt($currentDateTime);

        $feedback->addFeature($feature);
        $feature->setScoreUpByOne();

        $this->manager->persist($feedback);
        $this->manager->flush();
    }

}