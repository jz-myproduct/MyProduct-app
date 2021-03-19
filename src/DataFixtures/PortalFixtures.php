<?php


namespace App\DataFixtures;


use App\Entity\Company;
use App\Entity\Portal;
use App\Service\SlugService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PortalFixtures extends Fixture implements DependentFixtureInterface
{

    private $passwordEncoder;

    private $slugService;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, SlugService $slugService)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->slugService = $slugService;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {

        foreach ($this->getData() as $data)
        {
            $portal = new Portal();

            $portal->setName($data['name']);
            $portal->setSlug(
                $this->slugService->createInitialPortalSlug($data['name'])
            );
            $portal->setCreatedAt(new \DateTime());
            $portal->setUpdatedAt(new \DateTime());
            $portal->setDisplay($data['display']);
            $portal->setCompany(
                $this->getReference('company-'.strtolower($data['name']))
            );

            $manager->persist($portal);

            $this->setReference('portal-'.strtolower($data['name']), $portal);
        }

        $manager->flush();

    }

    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class,
        ];
    }


    private function getData()
    {
        return [
            ['name' => 'Microsoft', 'display' => true],
            ['name' => 'Apple', 'display' => false]
        ];
    }
}