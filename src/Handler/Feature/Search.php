<?php


namespace App\Handler\Feature;


use App\Entity\Company;
use App\Entity\FeatureState;
use App\FormRequest\Feature\ListFilterRequest;
use App\FormRequest\Feature\RoadmapFilterRequest;
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

    public function handleList(Company $company, ListFilterRequest $formRequest)
    {

        $state = $this->manager->getRepository(FeatureState::class)->find($formRequest->state);

        return $this->router->generate('bo_feature_list', [
            'slug' => $company->getSlug(),
            'state_slug' => $state ? $state->getSlug() : null,
            'tags'=> $formRequest->tags,
            'fulltext' => $formRequest->fulltext
        ]);

    }

    public function handleRoadmap(Company $company, RoadmapFilterRequest $formRequest)
    {

        return $this->router->generate('bo_feature_list_roadmap', [
            'slug' => $company->getSlug(),
            'tags'=> $formRequest->tags,
            'fulltext' => $formRequest->fulltext
        ]);

    }


}