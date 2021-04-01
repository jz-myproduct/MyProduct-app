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
                $feature->setName('Vylepšit '.$name);
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
            'Nové features',
            'Vylepšení stávajích features'
        ];
    }
}