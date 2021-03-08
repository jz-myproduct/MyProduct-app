<?php


namespace App\Handler\Insight;


use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
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

    public function handle(Insight $insight, Feedback $feedback, Feature $feature)
    {
        $insight->setFeedback($feedback);
        $insight->setFeature($feature);

        $feature->setScoreUpBy(
            $insight->getWeight()->getWeight()
        );

        $this->manager->persist($insight);
        $this->manager->flush();
    }

}