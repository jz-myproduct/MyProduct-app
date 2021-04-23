<?php


namespace App\DataFixtures;

use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use App\Entity\InsightWeight;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InsightFixtures extends Fixture implements DependentFixtureInterface
{

    private static $companies = ['Microsoft', 'Apple'];
    private static $weights = ['Not important', 'Nice-to-have', 'Important', 'Critical'];

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::$companies as $company)
        {
            foreach ($this->getFeatureDate() as $featureName)
            {
                $addedFeedbacks = [];
                for($i = 0; $i < rand(0, 15); $i++)
                {
                    $feedbackName = rand(1,40);
                    if(in_array($feedbackName, $addedFeedbacks)){
                        continue;
                    }

                    array_push($addedFeedbacks, $feedbackName);

                    /** @var Feature $feature */
                    $feature = $this->getReference('feature-'.strtolower($company).'-'.strtolower($featureName));

                    /** @var InsightWeight $weight */
                    $weight = $this->getReference('insightWeight-'.strtolower(self::$weights[rand(0,3)]));

                    /** @var Feedback $feedback */
                    $feedback = $this->getReference('feedback-'.strtolower($company).'-'.strtolower($feedbackName));

                    $feature->setScoreUpBy($weight->getNumber());

                    $insight = new Insight();
                    $insight->setFeature($feature);
                    $insight->setFeedback($feedback);
                    $insight->setWeight($weight);

                    if($feature->getPortalFeature()){

                        if($feedback->getFromPortal())
                        {
                            $feature->getPortalFeature()->setFeedbackCountUpByOne();
                        }
                    }

                    $manager->persist($insight);
                }
            }

            $manager->flush();
        }

    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            FeedbackFixtures::class,
            FeatureFixtures::class,
            InsightWeightFixtures::class,
            PortalFeatureFixtures::class
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
}