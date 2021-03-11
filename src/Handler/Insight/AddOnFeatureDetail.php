<?php


namespace App\Handler\Insight;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Insight;
use Doctrine\ORM\EntityManagerInterface;

class AddOnFeatureDetail
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var \App\Handler\Feedback\Add
     */
    private $feedbackHandler;

    public function __construct(EntityManagerInterface $manager, \App\Handler\Feedback\Add $feedbackHandler)
    {
        $this->manager = $manager;
        $this->feedbackHandler = $feedbackHandler;
    }

    public function handle(Insight $insight, Feature $feature)
    {
        $feedback = $this->feedbackHandler->handle(
            $insight->getFeedback(),
            $feature->getCompany()
        );
        $insight->setFeature($feature);

        $feature->setScoreUpBy(
            $insight->getWeight()->getWeight()
        );

        $this->manager->persist($feedback);
        $this->manager->persist($insight);

        $this->manager->flush();
    }


}