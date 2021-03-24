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
            'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam erat volutpat. Morbi leo mi, nonummy eget tristique non, rhoncus non leo. Duis risus. Nulla pulvinar eleifend sem. Duis condimentum augue id magna semper rutrum. Vestibulum erat nulla, ullamcorper nec, rutrum non, nonummy ac, erat. In sem justo, commodo ut, suscipit at, pharetra vitae, orci. Duis condimentum augue id magna semper rutrum. Nulla non arcu lacinia neque faucibus fringilla. Aliquam erat volutpat. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Fusce wisi.Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Fusce suscipit libero eget elit. Donec quis nibh at felis congue commodo. Duis condimentum augue id magna semper rutrum. Cras pede libero, dapibus nec, pretium sit amet, tempor quis. Aliquam ornare wisi eu metus. Phasellus et lorem id felis nonummy placerat. Fusce tellus. Praesent in mauris eu tortor porttitor accumsan. Suspendisse sagittis ultrices augue. Etiam bibendum elit eget erat.',
            'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam erat volutpat. Morbi leo mi, nonummy eget tristique non, rhoncus non leo. Duis risus. Nulla pulvinar eleifend sem. Duis condimentum augue id magna semper rutrum. Vestibulum erat nulla, ullamcorper nec, rutrum non, nonummy ac, erat. In sem justo, commodo ut, suscipit at, pharetra vitae, orci. Duis condimentum augue id magna semper rutrum. Nulla non arcu lacinia neque faucibus fringilla. Aliquam erat volutpat. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Fusce wisi.Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Fusce suscipit libero eget elit. Donec quis nibh at felis congue commodo. Duis condimentum augue id magna semper rutrum. Cras pede libero, dapibus nec, pretium sit amet, tempor quis. Aliquam ornare wisi eu metus. Phasellus et lorem id felis nonummy placerat. Fusce tellus. Praesent in mauris eu tortor porttitor accumsan. Suspendisse sagittis ultrices augue. Etiam bibendum elit eget erat.',
            'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam erat volutpat. Morbi leo mi, nonummy eget tristique non, rhoncus non leo. Duis risus. Nulla pulvinar eleifend sem. Duis condimentum augue id magna semper rutrum. Vestibulum erat nulla, ullamcorper nec, rutrum non, nonummy ac, erat. In sem justo, commodo ut, suscipit at, pharetra vitae, orci. Duis condimentum augue id magna semper rutrum. Nulla non arcu lacinia neque faucibus fringilla. Aliquam erat volutpat. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Fusce wisi.Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Fusce suscipit libero eget elit. Donec quis nibh at felis congue commodo. Duis condimentum augue id magna semper rutrum. Cras pede libero, dapibus nec, pretium sit amet, tempor quis. Aliquam ornare wisi eu metus. Phasellus et lorem id felis nonummy placerat. Fusce tellus. Praesent in mauris eu tortor porttitor accumsan. Suspendisse sagittis ultrices augue. Etiam bibendum elit eget erat.',
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