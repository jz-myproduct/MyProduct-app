<?php


namespace App\View\BackOffice\Insight;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use App\Handler\Insight\Redirect;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class AddEditView
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function addFromFeature(Feedback $feedback, Feature $feature, FormView $formView)
    {

        $array = $this->prepareArray($feedback, $feature, $formView);


        // TODO get absolute URL

        return $array;

    }

    public function editFromFeedback(Insight $insight, FormView $formView, Redirect $handler)
    {

        $array = $this->prepareArray($insight->getFeedback(), $insight->getFeature(), $formView);

        // TODO get absolute URL

        return $array;

    }

    public function editFromFeature(Insight $insight, FormView $formView, Redirect $handler)
    {

        $array = $this->prepareArray($insight->getFeedback(), $insight->getFeature(), $formView);

        // TODO get absolute URL

        return $array;

    }

    private function prepareArray(Feedback $feedback, Feature $feature, FormView $formView)
    {

        return [
            'form' => $formView,
            'feature' => $feature,
            'feedback' => $feedback,
        ];

    }

}