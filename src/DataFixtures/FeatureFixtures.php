<?php


namespace App\DataFixtures;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\FeatureState;
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

        foreach ($this->getDate() as $name)
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

                $manager->persist($feature);

                $this->setReference('feature-'.strtolower($name), $feature);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            FeatureStateFixtures::class,
            CompanyFixtures::class
        ];
    }

    private function getDate()
    {
        return [
            'Přihlašování pro firmy',
            'Registraci',
            'Vyhledávání pomocí parametrů',
            'UX',
            'Design',
            'Obrázky',
            'Rychlost webu',
            'Zákaznickou podporu',
            'Mailing',
            'Onboarding pro uživatele',
            'Zabezpečení hesel',
            'Informování o nových vylepšeních',
            'Mobilní verzi'
        ];
    }
}