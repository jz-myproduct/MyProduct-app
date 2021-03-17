<?php


namespace App\Handler\FeatureTag;


use App\Entity\Company;
use App\Entity\FeatureTag;
use App\FormRequest\FeatureTag\AddEditRequest;
use App\Service\SlugService;
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

    public function handle(AddEditRequest $request, Company $company)
    {
        $featureTag = new FeatureTag();

        $featureTag->setName($request->name);
        $featureTag->setSlug(
            $this->slugService->createCommonSlug(
                $request->name
            )
        );
        $featureTag->setCompany($company);

        $this->manager->persist($featureTag);
        $this->manager->flush();
    }

}