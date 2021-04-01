<?php


namespace App\DataFixtures;


use App\Entity\PortalFeatureState;
use App\Service\SlugService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PortalFeatureStateFixtures extends Fixture
{

    /**
     * @var SlugService
     */
    private $slugService;

    public function __construct(SlugService $slugService)
    {
        $this->slugService = $slugService;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {

        foreach ($this->getData() as $data)
        {
            $portalFeatureState = new PortalFeatureState();
            $portalFeatureState->setName($data['name']);
            $portalFeatureState->setSlug(
                $this->slugService->createCommonSlug($data['name'])
            );
            $portalFeatureState->setPosition($data['position']);

            $manager->persist($portalFeatureState);

            $this->setReference(
                'portalFeatureState-'.strtolower(str_replace(' ', '-',$data['name'])),
                $portalFeatureState
            );
        }

        $manager->flush();
    }

    private function getData()
    {
        return [
            ['name' => 'Nápady', 'position' => 1],
            ['name' => 'Připravujeme', 'position' => 2],
            ['name' => 'Hotovo', 'position' => 3]
        ];
    }
}