<?php


namespace App\Services;


use App\Entity\Feedback;
use App\Entity\Test;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use phpDocumentor\Reflection\Types\Null_;

class FeatureEventListener
{
    /**
     * @var FeatureScoreService
     */


    private $scoreService;

    public function __construct(FeatureScoreService $scoreService)
    {
        $this->scoreService = $scoreService;
    }

    /*
    public onFeedbackUpdatedEvent()
    {
    }


    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->object = $args->getObject();
        return;
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if(!$this->object instanceof feedback){
            return;
        }

        $this->scoreService->recalculateScoreForFeatures();
        return;
    }
    */

    public function onFeedbackUpdatedEvent()
    {
        $this->scoreService->recalculateScoreForFeatures();
    }


}