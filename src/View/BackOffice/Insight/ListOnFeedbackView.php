<?php


namespace App\View\BackOffice\Insight;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\FeatureTag;
use App\Entity\Feedback;
use App\Entity\Insight;
use App\FormRequest\Insight\FilterOnFeedbackRequest;
use App\Handler\Insight\Redirect;
use App\Repository\FeatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormView;

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

    public function create(Company $company, Feedback $feedback, FormView $form, FilterOnFeedbackRequest $request)
    {

        $unrelatedFeatures = $this->manager->getRepository(Feature::class)
            ->findUnsedFeaturesForFeedback(
                $company,
                $this->manager->getRepository(FeatureTag::class)->findBy(['id' => $request->tags ]),
                $this->manager->getRepository(Feature::class)->findUsedFeaturesForFeedback($feedback),
                $request->fulltext
            );

        $insights = $this->manager->getRepository(Insight::class)->findInsightsForFeedback($feedback);

        return [
            'feedback' => $feedback,
            'insights' => $insights,
            'unrelatedFeatureList' => $unrelatedFeatures,
            'insightsCount' => sizeof($insights),
            'redirectToFeedback' => Redirect::getRedirectToFeedback(),
            'form' => $form
        ];
    }

}