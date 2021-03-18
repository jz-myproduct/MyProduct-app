<?php


namespace App\Handler\Insight;


use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use App\Entity\InsightWeight;
use App\Entity\PortalFeature;
use App\FormRequest\Feedback\AddEditRequest;
use App\FormRequest\Insight\AddFromFeatureRequest;
use App\FormRequest\Insight\AddFromFeedbackRequest;
use Doctrine\ORM\EntityManagerInterface;

class Add
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var \App\Handler\Feedback\Add
     */
    private $feedbackHandler;

    public function __construct(EntityManagerInterface $manager, \App\Handler\Feedback\Add $feedbackHandler)
    {
        $this->manager = $manager;
        $this->feedbackHandler = $feedbackHandler;
    }

    public function addFromFeature(AddFromFeatureRequest $request, Feature $feature)
    {
        $feedback = $this->feedbackHandler->addInsight(
            AddEditRequest::fromArray([
                'description' => $request->description,
                'source' => $request->source
            ]),
            $feature->getCompany()
        );

        $this->prepareInsight($request->weight, $feedback, $feature);

        $this->manager->flush();
    }

    public function addFromFeedback(AddFromFeedbackRequest $request, Feedback $feedback, Feature $feature)
    {
        $this->prepareInsight($request->weight, $feedback, $feature);

        $this->manager->flush();
    }

    public function addFromPortal(AddFromFeatureRequest $request, PortalFeature $portalFeature)
    {
        $feedback = $this->feedbackHandler->addInsightFromPortal(
            AddEditRequest::fromArray([
                'description' => $request->description,
                'source' => $request->source
            ]),
            $portalFeature->getFeature()->getCompany()
        );

        $this->prepareInsight($request->weight, $feedback, $portalFeature->getFeature());

        $portalFeature->setFeedbackCountUpByOne();

        $this->manager->flush();
    }


    private function prepareInsight(InsightWeight $weight, Feedback $feedback, Feature $feature)
    {
        $insight = new Insight();
        $insight->setWeight($weight);
        $insight->setFeedback($feedback);
        $insight->setFeature($feature);

        $this->manager->persist($insight);

        $this->updateFeatureScore($feature, $weight);

        return $insight;
    }

    private function updateFeatureScore(Feature $feature, InsightWeight $weight)
    {
        return $feature->setScoreUpBy(
            $weight->getNumber()
        );
    }

}