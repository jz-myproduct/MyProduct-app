<?php


namespace App\Handler\Feedback;


use App\Entity\Company;
use App\Entity\Feedback;
use App\Entity\Portal;
use App\Entity\PortalFeature;
use Doctrine\ORM\EntityManagerInterface;

class AddFeatureFeedbackOnPortal
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(Feedback $feedback, PortalFeature $portalFeature, Company $company)
    {
        $feedback->setCompany($company);
        $feedback->setIsNew(true);
        $feedback->setFromPortal(true);

        $currentDateTime = new \DateTime();
        $feedback->setCreatedAt($currentDateTime);
        $feedback->setUpdatedAt($currentDateTime);

        $feedback->addFeature(
            $portalFeature->getFeature()
        );
        $portalFeature->getFeature()->setScoreUpByOne();
        $portalFeature->setFeedbackCountUpByOne();

        $this->manager->persist($feedback);
        $this->manager->flush();
    }

}