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
            'Přihlašování pro firmy',
            'Registraci',
            'Vyhledávání pomocí parametrů',
            'UX',
            'Design',
            'Obrázky',
            'Rychlost webu',
            'Anglickou verzi',
            'Zákaznickou podporu',
            'Mailing',
            'Onboarding pro uživatele',
            'Zabezpečení hesel',
            'Informování o nových vylepšeních',
            'Mobilní verzi',
            'Nápovědu',
            'Víceúrovňový přístup pro adminy',
            'Mobilní aplikaci',
            'Homepage',
            'Záhlaví',
            'Zápatí',
            'Německou verzi',
            'Českou verzi',
            'Proces zapomenutého hesla',
            'Platební bránu',
            'Propagaci',
            'Náborový proces',
            'Průvodce profilem',
            'Inbox',
            'Textový editor',
            'Cashback program',
            'Cashback program pro B2B klienty',
            'Filtrování',
            'Public API',
            'Interní chat',
            'Interní poznámky',
            'Testy znalostí',
            'Online manual',
            'Nahrávání obrázků',
            'E2E testy',
            'Unit testy',
            'Dostupnost',
            'Nová platební brána',
            'Lepší profil',
            'Public profil',
            'Opravit překlady',
            'Vylepšit cachování',
            'B2B analytika',
            'B2C analytika',
            'Přihlašování pomocí Google',
            'Přihlašování pomocí Facebook',
            'Přihlašování pomocí Twitter',
            'Přihlašování pomocí LinkedIn',
            'Zjednodušená registrace'
        ];
    }
}