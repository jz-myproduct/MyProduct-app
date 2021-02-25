<?php


namespace App\Handler\PortalFeature;


use App\Entity\Feature;
use App\Entity\PortalFeature;
use App\Services\SlugService;
use Doctrine\ORM\EntityManagerInterface;

class Add
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var SlugService
     */
    private $slugService;

    public function __construct(EntityManagerInterface $manager, SlugService $slugService)
    {
        $this->manager = $manager;
        $this->slugService = $slugService;
    }

    public function handle(PortalFeature $portalFeature, Feature $feature)
    {
        $currentDateTime = new \DateTime();

        $portalFeature->setFeedbackCount(0);
        $portalFeature->setSlug(
            $this->slugService->createCommonSlug(
                $portalFeature->getName()
            )
        );

        $portalFeature->setFeature($feature);

        $portalFeature->setCreatedAt($currentDateTime);
        $portalFeature->setUpdatedAt($currentDateTime);

        $this->manager->persist($portalFeature);
        $this->manager->flush();
    }

}