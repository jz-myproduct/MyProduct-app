<?php


namespace App\Handler\Feedback;


use App\Entity\Company;
use App\Entity\Feedback;
use Symfony\Component\Routing\RouterInterface;

class SwitchStatusRedirect
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function handle(
        String $param,
        Feedback $feedback,
        Company $company)
    {

        // TODO ty stringy by měli být uloženy někde jako konstanty (ViewClass)

        if ($param === 'bo_feedback_list') {

            return $this->router->generate('bo_feedback_list', [
               'slug' => $company->getSlug()
            ]);

        }

        if ($param === 'bo_feedback_detail') {

            return $this->router->generate('bo_feedback_detail', [
                'company_slug' => $company->getSlug(),
                'feedback_id' => $feedback->getId()
            ]);
        }

        if ($param === 'bo_feedback_features') {

            return $this->router->generate('bo_feedback_features', [
                'company_slug' => $company->getSlug(),
                'feedback_id' => $feedback->getId()
            ]);
        }

        return $this->router->generate('bo_home', [
            'slug' => $company->getSlug()
        ]);

    }

}