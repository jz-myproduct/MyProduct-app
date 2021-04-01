<?php


namespace App\View\BackOffice\Insight;


use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use App\Handler\Insight\Redirect;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;

class AddEditView
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Redirect
     */
    private $redirect;

    public function __construct(RouterInterface $router, Redirect $redirect)
    {
        $this->router = $router;
        $this->redirect = $redirect;
    }


    public function addFromFeedback(Feedback $feedback, Feature $feature, FormView $formView)
    {

        $array = $this->prepareArray($feedback, $feature, $formView);

        $array['backUrl'] = $this->generateBackURLForFeedback($feedback);

        return $array;

    }

    public function edit(Insight $insight, FormView $formView, string $param = null)
    {

        $array = $this->prepareArray($insight->getFeedback(), $insight->getFeature(), $formView);

        $array['backUrl'] = $this->redirect->handle(
            $insight,
            $insight->getFeedback()->getCompany(),
            $param
        );

        return $array;

    }

    private function generateBackURLForFeedback(Feedback $feedback)
    {
        return
            $this->router->generate('bo_insight_feedback_list', [
               'company_slug' => $feedback->getCompany()->getSlug(),
                'feedback_id' => $feedback->getId()
            ]);
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