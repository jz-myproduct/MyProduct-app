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

    public static $list = 'l';
    public static $detail = 'd';

    public function handle(
        Feedback $feedback,
        Company $company,
        String $pageRedirect = null,
        String $isNew = null,
        String $fulltext = null)
    {

        if ($pageRedirect === self::$list) {

            return $this->router->generate('bo_feedback_list', [
               'slug' => $company->getSlug(),
                'fulltext' => $fulltext,
                'isNew' => $isNew
            ]);

        }

        if ($pageRedirect === self::$detail) {

            return $this->router->generate('bo_feedback_detail', [
                'company_slug' => $company->getSlug(),
                'feedback_id' => $feedback->getId()
            ]);
        }

        return $this->router->generate('bo_feedback_list', [
            'slug' => $company->getSlug()
        ]);

    }

}