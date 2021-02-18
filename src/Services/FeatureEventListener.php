<?php


namespace App\Services;


use App\Entity\Feedback;
use App\Entity\Test;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class FeatureEventListener
{
    /**
     * @var FeatureScoreService
     */
    private $scoreService;

    private $entityManager;

    public function __construct(FeatureScoreService $scoreService, EntityManagerInterface $entityManager)
    {
        $this->scoreService = $scoreService;
        $this->entityManager = $entityManager;
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Feedback) {
            return;
        }


        /*
        $test = new Test();
        $test->setName('test');
        $this->entityManager->persist($test);
        $this->entityManager->flush(); */


        $this->scoreService->recalculateScoreForFeatures();
    }
}