<?php


namespace App\Handler\FeatureTag;


use App\Entity\FeatureTag;
use App\Services\SlugService;
use Doctrine\ORM\EntityManagerInterface;

class Edit
{
    /**
     * @var SlugService
     */
    private $slugService;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager, SlugService $slugService)
    {
        $this->manager = $manager;
        $this->slugService = $slugService;
    }

    public function handle(FeatureTag $featureTag)
    {
        $featureTag->setSlug(
            $this->slugService->createCommonSlug(
                $featureTag->getName()
            )
        );

        $this->manager->flush();
    }

}