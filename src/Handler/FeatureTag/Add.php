<?php


namespace App\Handler\FeatureTag;


use App\Entity\Company;
use App\Entity\FeatureTag;
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

    public function handle(FeatureTag $featureTag, Company $company)
    {
        $featureTag->setSlug(
            $this->slugService->createCommonSlug(
                $featureTag->getName()
            )
        );
        $featureTag->setCompany($company);

        $this->manager->persist($featureTag);
        $this->manager->flush();
    }

}