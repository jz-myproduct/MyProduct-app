<?php


namespace App\DataFixtures;

use App\Entity\Feature;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FeatureFixtures extends Fixture implements DependentFixtureInterface
{
    private static $companies = ['microsoft', 'apple'];
    private static $states = ['Idea', 'Upcoming', 'In-progress', 'Done'];
    private static $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi scelerisque ipsum mi, at 
    dapibus risus auctor in. Pellentesque ac facilisis dui, in dictum odio. Nunc ac tellus id erat feugiat blandit.
     Nullam pharetra pellentesque ante at dapibus. Phasellus non luctus felis. Etiam vel mi auctor, hendrerit lacus aliquam, 
     feugiat nulla. Donec ut sem condimentum, rhoncus quam et, finibus quam. Aenean congue blandit gravida. Curabitur blandit 
     pellentesque commodo.';

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {

        foreach ($this->getFeatureDate() as $name)
        {
            foreach(self::$companies as $company)
            {
                $feature = new Feature();
                $feature->setName('Improve '.$name);
                $feature->setDescription(self::$description);
                $feature->setCreatedAt(new \DateTime());
                $feature->setUpdatedAt(new \DateTime());
                $feature->setCompany($this->getReference('company-'.strtolower($company)));
                $feature->setState($this->getReference('featureState-'.strtolower(self::$states[rand(0,3)])));
                $feature->setInitialScore();

                $addedTags = [];
                $tagsCount = count($this->getTagData());

                for($i = 0; $i < rand(0, $tagsCount); $i++)
                {
                    $tag = rand(0, $tagsCount-1);

                    if (in_array($tag, $addedTags)) {
                        continue;
                    }

                    array_push($addedTags, $tag);

                    $feature->addTag(
                        $this->getReference(
                            'featureTag-'.strtolower($company).'-'.strtolower( ($this->getTagData())[$tag] )
                        )
                    );
                }

                $manager->persist($feature);

                $this->setReference('feature-'.strtolower($company).'-'.strtolower($name), $feature);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            FeatureStateFixtures::class,
            CompanyFixtures::class,
            FeatureTagFixtures::class
        ];
    }

    private function getFeatureDate()
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

    public function getTagData()
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