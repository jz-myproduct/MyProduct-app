<?php


namespace App\View\BackOffice\Feature;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\FeatureState;
use App\Entity\FeatureTag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormView;

class RoadmapView
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(Company $company, FormView $form, $tagsParam = [])
    {
        $states = $this->manager->getRepository(FeatureState::class)->findAll();

        return [
            'features' => $this->prepareFeaturesData($company, $states, $tagsParam),
            'columnWidth' => $this->prepareColumnWidth($states),
            'form' => $form,
            'tagsExist' => $company->getFeatureTags()->toArray() ? true : false

        ];
    }

    private function prepareFeaturesData(Company $company, $states, $tagsParam = [])
    {
        $tags = $this->manager->getRepository(FeatureTag::class)
            ->findBy( ['id' => $tagsParam ] );

        $features = array();

        foreach($states as $featureState)
        {
            $features[] = [

                'state' => $featureState->getName(),
                'features' =>
                    $this->manager->getRepository(Feature::class)
                        ->findCompanyFeaturesByTag($tags, $company, $featureState),
                'isFirst' =>
                    $featureState === $this->manager->getRepository(FeatureState::class)->findInitialState() ?
                        true : false,
                'isLast' =>
                    $featureState === $this->manager->getRepository(FeatureState::class)->findLastState() ?
                        true : false
            ];
        }

        return $features;
    }

    private function prepareColumnWidth($states)
    {
        return round(100 / count($states));
    }

}