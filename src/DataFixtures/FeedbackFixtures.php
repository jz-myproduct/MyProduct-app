<?php


namespace App\DataFixtures;


use App\Entity\Company;
use App\Entity\Feedback;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FeedbackFixtures extends Fixture implements DependentFixtureInterface
{

    private static $companies = ['Microsoft', 'Apple'];
    private static $text =
        [
            'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas aliquet accumsan leo. Morbi scelerisque luctus velit. Nunc tincidunt ante vitae massa',
            'Filip',
            'Honza',
            'Marek',
            'Tereza',
            'Petra',
            'Domonika'
        ];

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {

        foreach(self::$companies as $company) {

            for ($i = 1; $i <= 40; $i++) {

                $feedback = new Feedback();
                $feedback->setDescription(self::$text[rand(0, 6)]);
                $feedback->setSource(self::$text[rand(0, 6)]);
                $feedback->setCreatedAt(new \DateTime());
                $feedback->setUpdatedAt(new \DateTime());
                $feedback->setIsNew(rand(0, 1));
                $feedback->setFromPortal(rand(0, 1));
                $feedback->setCompany($this->getReference('company-' . strtolower($company)));

                $manager->persist($feedback);
                $this->setReference('feedback-' . strtolower($company) . '-' .$i, $feedback);
            }
        }


        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class,
        ];
    }
}