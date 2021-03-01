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
    
}