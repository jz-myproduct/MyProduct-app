<?php


namespace App\Handler\FeatureTag;


use App\Entity\FeatureTag;
use App\FormRequest\FeatureTag\AddEditRequest;
use App\Service\SlugService;
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

    public function handle(AddEditRequest $request, FeatureTag $featureTag)
    {
        $featureTag->setName($request->name);

        $featureTag->setSlug(
            $this->slugService->createCommonSlug(
                $request->name
            )
        );

        $this->manager->flush();
    }

}