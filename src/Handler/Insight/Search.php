<?php


namespace App\Handler\Insight;


use App\Entity\Company;
use App\Entity\Feedback;
use App\FormRequest\Insight\FilterOnFeedbackRequest;
use App\View\BackOffice\Insight\ListOnFeedbackView;
use Symfony\Component\Routing\RouterInterface;

class Search
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function handle(FilterOnFeedbackRequest $request, Company $company, Feedback $feedback)
    {

        return $this->router->generate('bo_insight_feedback_list', [
            'company_slug' => $company->getSlug(),
            'feedback_id' => $feedback->getId(),
            'tags' => $request->tags,
            'fulltext' => $request->fulltext,
            'state' => $request->state,
            '_fragment' => ListOnFeedbackView::$scrollTo
        ]);

    }

}