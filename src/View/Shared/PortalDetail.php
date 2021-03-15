<?php


namespace App\View\Shared;


use App\Entity\Company;
use App\Entity\PortalFeature;
use App\Entity\PortalFeatureState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormView;

class PortalDetail
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(Company $company, FormView $form = null, $state = null)
    {
        $currentState = $state ?? $this->manager->getRepository(PortalFeatureState::class)
                                    ->findInitialState();

        $stateList = $this->manager->getRepository(PortalFeatureState::class)->findAll();

        $portalFeatureList = $this->manager->getRepository(PortalFeature::class)
            ->findFeaturesForPortalByState($company, $currentState);

        $array = [
            'currentState' => $currentState,
            'stateList' => $stateList,
            'portalFeatureList' => $portalFeatureList,
            'portal' => $company->getPortal(),
        ];

        if($form){
            $array['form'] = $form;
        }

        return $array;
    }

}