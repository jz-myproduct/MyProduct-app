<?php


namespace App\Handler\Feedback;


use App\Entity\Feature;
use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteRelation
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(Feedback $feedback, Feature $feature)
    {
        $feedback->removeFeature($feature);
        $feature->setScoreDownByOne();

        $this->manager->flush();
    }

}