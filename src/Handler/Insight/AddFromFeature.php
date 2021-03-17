<?php


namespace App\Handler\Insight;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use App\FormRequest\Feedback\AddEditRequest;
use App\FormRequest\Insight\AddFromFeatureRequest;
use Doctrine\ORM\EntityManagerInterface;

class AddFromFeature
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    private $feedbackHandler;

    public function __construct(EntityManagerInterface $manager, \App\Handler\Feedback\AddFromFeature $feedbackHandler)
    {
        $this->manager = $manager;
        $this->feedbackHandler = $feedbackHandler;
    }

    public function handle(AddFromFeatureRequest $request, Feature $feature)
    {

        $feedback = $this->feedbackHandler->handle(
            AddEditRequest::fromArray([
                'description' => $request->description,
                'source' => $request->source
            ]),
            $feature->getCompany()
        );

        $insight = new Insight();
        $insight->setFeature($feature);
        $insight->setFeedback($feedback);
        $insight->setWeight($request->weight);

        $feature->setScoreUpBy(
            $insight->getWeight()->getNumber()
        );

        $this->manager->persist($feedback);
        $this->manager->persist($insight);

        $this->manager->flush();
    }


}