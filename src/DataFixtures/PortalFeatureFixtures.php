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
    private static $states = ['Nápady', 'Připravujeme', 'Hotovo'];
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
                $portalFeature->setName('Vylepšit '.$name);
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