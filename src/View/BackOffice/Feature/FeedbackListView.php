<?php


namespace App\View\BackOffice\Feature;


use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormView;

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

    public function create(Feature $feature, FormView $form)
    {
        $feedbackCount = $this->manager->getRepository(Insight::class)
            ->getInsightsCountForFeature($feature);

        $insightList = $this->manager->getRepository(Insight::class)->findBy(['feature' => $feature]);

        return [
            'feature' => $feature,
            'insightList' => $insightList,
            'feedbackCount' => $feedbackCount,
            'form' => $form
        ];
    }

}