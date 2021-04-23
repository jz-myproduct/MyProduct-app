<?php


namespace App\DataFixtures;

use App\Entity\PortalFeature;
use App\Service\FeatureUtils;
use App\Service\SlugService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PortalFeatureFixtures extends Fixture implements DependentFixtureInterface
{

    private static $companies = ['microsoft', 'apple'];
    private static $states = ['Ideas', 'In-progress', 'Done'];
    private static $description
        = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi scelerisque ipsum mi, at 
    dapibus risus auctor in. Pellentesque ac facilisis dui, in dictum odio. Nunc ac tellus id erat feugiat blandit.
     Nullam pharetra pellentesque ante at dapibus. Phasellus non luctus felis. Etiam vel mi auctor, hendrerit lacus aliquam, 
     feugiat nulla. Donec ut sem condimentum, rhoncus quam et, finibus quam. Aenean congue blandit gravida. Curabitur blandit 
     pellentesque commodo.';

    /**
     * @var SlugService
     */
    private $slugService;
    /**
     * @var FeatureUtils
     */
    private $portalFeatureUtils;

    public function __construct(SlugService $slugService, FeatureUtils $portalFeatureUtils)
    {
        $this->slugService = $slugService;
        $this->portalFeatureUtils = $portalFeatureUtils;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getDate() as $name)
        {
            foreach(self::$companies as $company)
            {
                $portalFeature = new PortalFeature();
                $portalFeature->setName('Improve '.$name);
                $portalFeature->setDescription(self::$description);
                $portalFeature->setSlug(
                    $this->slugService->createCommonSlug($name)
                );
                $portalFeature->setState(
                    $this->getReference(
                        'portalFeatureState-'.strtolower(str_replace(' ', '-', self::$states[rand(0,2)]))
                    )
                );
                $portalFeature->setCreatedAt(new \DateTime());
                $portalFeature->setUpdatedAt(new \DateTime());
                $portalFeature->setDisplay(rand(0,1));
                $portalFeature->setFeature(
                    $this->getReference('feature-'.strtolower($company).'-'.strtolower($name))
                );
                $portalFeature->setFeedbackCount(0);
                $portalFeature->setImage($this->getReference('image-'.strtolower($company).'-'.strtolower($name)));

                $manager->persist($portalFeature);

                $this->setReference('portalFeature-'.strtolower($company).'-'.strtolower($name), $portalFeature);
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
            CompanyFixtures::class,
            FeatureFixtures::class,
            PortalFeatureStateFixtures::class,
            FileFixtures::class
        ];
    }

    private function getDate()
    {
        return [
            'Login for B2B',
            'Registration for B2B',
            'Fulltext',
            'UX',
            'Design',
            'Images',
            'Page speed',
            'Eng version',
            'Helpdesk',
            'Mailing',
            'Onboarding',
            'Security',
            'PR',
            'Responsive version',
            'FAQ',
            'Multilevel access',
            'Mobile app',
            'Homepage',
            'Header',
            'Footer',
            'Ne version',
            'Cs version',
            'Password change',
            'Payment Gateway',
            'Propagation',
            'Filtering',
            'Profile guide',
            'Inbox',
            'Text editor',
            'Cashback',
            'Cashback for B2B',
            'New design',
            'Public API',
            'Chat',
            'Notes',
            'Quizzes',
            'Online manual',
            'Image uploading',
            'E2E tests',
            'Unit tests',
            'Availability',
            'New payment gateway',
            'Improve profil',
            'Public profil',
            'Fix translations',
            'Improve caching',
            'B2B analytics',
            'B2C analytics',
            'Login via Google',
            'Login via Facebook',
            'Login via Twitter',
            'Login via LinkedIn',
            'Better registration'
        ];
    }
}