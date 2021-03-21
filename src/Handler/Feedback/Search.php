<?php


namespace App\Handler\Feedback;


use App\Entity\Company;
use App\FormRequest\Feedback\ListFilterRequest;
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

    public function handle(ListFilterRequest $request, Company $company)
    {

        return $this->router->generate('bo_feedback_list', [
            'slug' => $company->getSlug(),
            'fulltext' => $request->fulltext,
            'isNew' => $request->isNew
        ]);

    }

}