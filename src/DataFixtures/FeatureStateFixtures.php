<?php


namespace App\DataFixtures;

use App\Entity\FeatureState;
use App\Service\SlugService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FeatureStateFixtures extends Fixture
{

    /**
     * @var SlugService
     */
    private $slugService;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, SlugService $slugService)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->slugService = $slugService;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {

        foreach ($this->getData() as $data)
        {
            $featureState = new FeatureState();
            $featureState->setName($data['name']);
            $featureState->setSlug(
                $this->slugService->createCommonSlug($data['name'])
            );
            $featureState->setPosition($data['position']);
            $featureState->setColor($data['color']);

            $manager->persist($featureState);

            $this->setReference('featureState-'.strtolower($data['name']), $featureState);
        }

        $manager->flush();
    }

    public function getData()
    {
        return [
            ['name' => 'Idea', 'position' => 1, 'color' => 'indianred'],
            ['name' => 'Upcoming', 'position' => 2, 'color' => 'cornflowerblue'],
            ['name' => 'In-progress', 'position' => 3, 'color' => 'darkorange'],
            ['name' => 'Done', 'position' => 4, 'color' => 'seagreen']
        ];
    }
}