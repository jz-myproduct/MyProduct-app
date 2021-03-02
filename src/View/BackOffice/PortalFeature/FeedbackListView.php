<?php


namespace App\View\BackOffice\PortalFeature;


use App\Entity\Feature;
use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;

class FeedbackListView
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(Feature $feature)
    {
        return $feedback = $this->manager->getRepository(Feedback::class)
               ->getFeatureFeedback($feature);
    }

}