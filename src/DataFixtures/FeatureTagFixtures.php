<?php


namespace App\DataFixtures;


use App\Entity\FeatureTag;
use App\Service\SlugService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FeatureTagFixtures extends Fixture implements DependentFixtureInterface
{

    private static $companies = ['microsoft', 'apple'];
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
        foreach(self::$companies as $company)
        {
            foreach ($this->getData() as $name)
            {
                $tag = new FeatureTag();
                $tag->setName($name);
                $tag->setSlug(
                    $this->slugService->createCommonSlug($name)
                );
                $tag->setCompany($this->getReference('company-'.strtolower($company)));

                $manager->persist($tag);

                $this->setReference('featureTag-'.strtolower($company).'-'.strtolower($name), $tag);
            }
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            CompanyFixtures::class
        ];
    }

    private function getData()
    {
        return [
            'Back-office',
            'Front-office',
            'Refactoring',
            'B2B',
            'B2C',
            'UX',
            'Marketing',
            'DevOps',
            'Performance',
            'Onboarding',
            'New features',
            'Improve current features'
        ];
    }
}