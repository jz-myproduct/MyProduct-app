<?php


namespace App\Handler\Insight;


use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use App\FormRequest\Insight\AddFromFeedbackRequest;
use Doctrine\ORM\EntityManagerInterface;

class AddFromFeedback
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(AddFromFeedbackRequest $request, Feedback $feedback, Feature $feature)
    {
        $insight = new Insight();
        $insight->setWeight($request->weight);

        $insight->setFeedback($feedback);
        $insight->setFeature($feature);

        $feature->setScoreUpBy(
            $insight->getWeight()->getNumber()
        );

        $this->manager->persist($insight);
        $this->manager->flush();
    }

}