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
    /**
     * @var FeedbackCountService
     */
    private $countService;

    public function __construct(FeatureScoreService $scoreService, FeedbackCountService $countService)
    {
        $this->scoreService = $scoreService;
        $this->countService = $countService;
    }

    public function onFeedbackUpdatedEvent()
    {
        $this->scoreService->recalculateScoreForFeatures();
        $this->countService->recalculateFeedbackCountForPortalFeature();
    }


}