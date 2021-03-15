<?php


namespace App\Handler\Feature;


use App\Entity\Company;
use App\Entity\FeatureState;
use App\FormRequest\FeatureStateFilterRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

class Search
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(EntityManagerInterface $manager, RouterInterface $router)
    {
        $this->manager = $manager;
        $this->router = $router;
    }

    public function handle(Company $company, FeatureStateFilterRequest $formRequest)
    {

        $state = $this->manager->getRepository(FeatureState::class)->find($formRequest->state);

        return $this->router->generate('bo_feature_list', [
            'slug' => $company->getSlug(),
            'state_slug' => $state ? $state->getSlug() : null,
            'tags'=> $formRequest->tags
        ]);

    }


}