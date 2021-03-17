<?php


namespace App\Handler\Insight;


use App\Entity\Feature;
use App\Entity\Insight;
use App\Entity\PortalFeature;
use App\FormRequest\Insight\AddFromFeatureRequest;
use Doctrine\ORM\EntityManagerInterface;

class AddFromPortal
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var \App\Handler\Feedback\AddFromPortal
     */
    private $feedbackHandler;

    public function __construct(EntityManagerInterface $manager, \App\Handler\Feedback\AddFromPortal $feedbackHandler)
    {
        $this->manager = $manager;
        $this->feedbackHandler = $feedbackHandler;
    }

    public function handle(AddFromFeatureRequest $request, PortalFeature $portalFeature)
    {
       $feedback = $this->feedbackHandler->handle(
            $request,
            $portalFeature->getFeature()->getCompany()
       );

       $portalFeature->setFeedbackCountUpByOne();

       $insight = new Insight();
       $insight->setWeight($request->weight);

       $insight->setFeedback($feedback);
       $insight->setFeature(
         $portalFeature->getFeature()
       );

       $insight->getFeature()->setScoreUpBy(
           $insight->getWeight()->getNumber()
       );

       $this->manager->persist($feedback);
       $this->manager->persist($insight);

       $this->manager->flush();
    }

}