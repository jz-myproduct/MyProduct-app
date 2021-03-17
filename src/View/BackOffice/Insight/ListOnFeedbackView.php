<?php


namespace App\View\BackOffice\Insight;


use App\Entity\Company;
use App\Entity\Feedback;
use App\Entity\Insight;
use Doctrine\ORM\EntityManagerInterface;

class ListOnFeedbackView
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(Company $company, Feedback $feedback)
    {
        $unrelatedFeatureList =  $this->manager->getRepository(Insight::class)
            ->getUnUsedFeaturesForFeedback($feedback, $company);

        $insightsCount = $this->manager->getRepository(Insight::class)
            ->getInsightsCountForFeedback($feedback);

        return [
            'feedback' => $feedback,
            'relatedFeatureList' => $this->prepareRelatedFeatures($feedback),
            'unrelatedFeatureList' => $unrelatedFeatureList,
            'insightsCount' => $insightsCount
        ];
    }

    private function prepareRelatedFeatures(Feedback $feedback)
    {
        $relatedFeatureList = array();

        foreach ($feedback->getInsights() as $insight)
        {
            array_push($relatedFeatureList, [

                'insight' => [ 'id' => $insight->getId(), 'name' => $insight->getWeight()->getName() ],
                'feature' => [ 'id' => $insight->getFeature()->getId(), 'name' => $insight->getFeature()->getName()]

            ]);
        }

        return $relatedFeatureList;

    }
}