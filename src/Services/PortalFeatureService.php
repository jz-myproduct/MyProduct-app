<?php


namespace App\Services;


use App\Entity\Company;
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