<?php


namespace App\View\BackOffice\Feedback;


use App\Entity\Company;
use App\Entity\Feedback;
use App\Entity\Insight;
use Doctrine\ORM\EntityManagerInterface;

class FeatureView
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

        $relatedFeatureList = array();

        foreach ($feedback->getInsights() as $insight)
        {
            array_push($relatedFeatureList, [
                'insight' => $insight->getWeight()->getName(),
                'name' => $insight->getFeature()->getName(),
                'id' => $insight->getFeature()->getId()
            ]);
        }

        return [
            'feedback' => $feedback,
            'relatedFeatureList' => $relatedFeatureList,
            'unrelatedFeatureList' => $unrelatedFeatureList
        ];
    }
}