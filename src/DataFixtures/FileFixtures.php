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