<?php


namespace App\Services;


use App\Entity\Company;
use App\Entity\Portal;
use App\Entity\PortalFeature;
use App\Entity\PortalFeatureState;
use Doctrine\ORM\EntityManagerInterface;

class PortalFeatureService
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function isAllowToBeDisplayed(PortalFeature $portalFeature, Portal $portal)
    {

        if(! $portal->getDisplay())
        {
            return false;
        }

        if(! $portalFeature->getDisplay())
        {
            return false;
        }

        if($portalFeature->getFeature()->getCompany() !== $portal->getCompany())
        {
            return false;
        }

        return true;

    }

    // TODO will be refactored and moved to View soon
    public function getArray(Company $company)
    {
        $array = array();

        foreach ($this->manager->getRepository(PortalFeatureState::class)->findAll() as $portalFeatureState)
        {
            $array[] = [
                'state' => $portalFeatureState->getName(),
                'features' => $this->manager
                    ->getRepository(PortalFeature::class)
                    ->findFeaturesForPortalByState($company, $portalFeatureState)
            ];
        }

        return $array;
    }

}