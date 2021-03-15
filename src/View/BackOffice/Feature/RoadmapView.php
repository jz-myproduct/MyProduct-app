<?php


namespace App\View\BackOffice\Feature;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\FeatureState;
use Doctrine\ORM\EntityManagerInterface;

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

    public function create(Company $company)
    {
        $states = $this->manager->getRepository(FeatureState::class)->findAll();

        return [
            'features' => $this->prepareFeaturesData($company, $states),
            'columnWidth' => $this->prepareColumnWidth($states)
        ];
    }

    private function prepareFeaturesData(Company $company, $states)
    {
        $features = array();

        foreach($states as $featureState)
        {
            $features[] = [

                'state' => $featureState->getName(),
                'features' =>
                    $this->manager->getRepository(Feature::class)->findBy([
                        'state' => $featureState,
                        'company' => $company
                    ]),
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