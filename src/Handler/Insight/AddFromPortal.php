<?php


namespace App\Handler\Insight;


use App\Entity\Feature;
use App\Entity\Insight;
use App\Entity\PortalFeature;
use Doctrine\ORM\EntityManagerInterface;

class AddFromPortal
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var \App\Handler\Feedback\AddFromPortal
     */
    private $feedbackHandler;

    public function __construct(EntityManagerInterface $manager, \App\Handler\Feedback\AddFromPortal $feedbackHandler)
    {
        $this->manager = $manager;
        $this->feedbackHandler = $feedbackHandler;
    }

    public function handle(Insight $insight, PortalFeature $portalFeature)
    {
       $feedback = $this->feedbackHandler->handle(
            $insight->getFeedback(),
            $portalFeature->getFeature()->getCompany()
       );

       $insight->setFeature(
         $portalFeature->getFeature()
       );

       $insight->getFeature()->setScoreUpBy(
           $insight->getWeight()->getWeight()
       );

       $this->manager->persist($feedback);
       $this->manager->persist($insight);

       $this->manager->flush();
    }

}