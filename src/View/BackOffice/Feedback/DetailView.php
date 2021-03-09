<?php


namespace App\View\BackOffice\Feedback;


use App\Entity\Feedback;
use App\Entity\Insight;
use Doctrine\ORM\EntityManagerInterface;

class DetailView
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(Feedback $feedback)
    {
        $insightsCount = $this->manager->getRepository(Insight::class)
            ->getInsightsCountForFeedback($feedback);

        return [
            'feedback' => $feedback,
            'insightsCount' => $insightsCount
        ];
    }


}