<?php


namespace App\DataFixtures;


use App\Entity\File;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FileFixtures extends Fixture
{

    private static $name = 'tapir-test-image.jpeg';
    private static $companies = ['microsoft', 'apple'];

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::$companies as $company)
        {
            foreach ($this->getDate() as $name)
            {
                $image = new File();
                $image->setName(self::$name);

                $manager->persist($image);
                $this->setReference('image-'.strtolower($company).'-'.strtolower($name), $image);
                $manager->flush();

            }
        }

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