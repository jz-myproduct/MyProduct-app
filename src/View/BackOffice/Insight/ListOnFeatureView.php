<?php


namespace App\View\BackOffice\Insight;


use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use App\Handler\Insight\Redirect;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormView;

class ListOnFeatureView
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

        $insightList = $this->manager->getRepository(Insight::class)->findInsightsForFeature($feature);

        return [
            'feature' => $feature,
            'insightList' => $insightList,
            'insightsCount' => sizeof($insightList),
            'form' => $form,
            'redirectToFeature' => Redirect::getRedirectToFeature()
        ];
    }

}