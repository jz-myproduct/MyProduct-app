<?php


namespace App\Handler\PortalFeature;


use App\Entity\PortalFeature;
use App\Services\SlugService;
use Doctrine\ORM\EntityManagerInterface;

class Edit
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

    public function handle(PortalFeature $portalFeature)
    {
        $portalFeature->setUpdatedAt(new \DateTime());
        $portalFeature->setSlug(
          $portalFeature->getName()
        );

        $this->manager->flush();
    }


}