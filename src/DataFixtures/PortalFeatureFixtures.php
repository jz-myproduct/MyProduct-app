<?php


namespace App\DataFixtures;


use App\Entity\PortalFeature;
use App\Entity\PortalFeatureState;
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

    public function __construct(SlugService $slugService)
    {
        $this->slugService = $slugService;
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
                $portalFeature->setName($name);
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
                $portalFeature->setFeedbackCount(rand(0,3));

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
            PortalFeatureStateFixtures::class
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
            'Nahrávání obrázků'
        ];
    }
}