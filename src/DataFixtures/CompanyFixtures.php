<?php


namespace App\DataFixtures;

use App\Entity\Company;
use App\Service\SlugService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CompanyFixtures extends Fixture
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
            $company = new Company();

            $company->setPassword(
                $this->passwordEncoder->encodePassword($company, $data['password'])
            );
            $company->setEmail($data['email']);
            $company->setName($data['name']);
            $company->setSlug(
                $this->slugService->createInitialCompanySlug($data['name'])
            );
            $company->setCreatedAt(new \DateTime());
            $company->setUpdatedAt(new \DateTime());
            $company->setRoles( [Company::getUserRole()] );

            $manager->persist($company);

            $this->setReference('company-'.strtolower($data['name']), $company);
        }

        $manager->flush();
    }

    private function getData()
    {
        return [
            ['name' => 'Microsoft', 'email' => 'h@h.hh', 'password' => 'heslo123'],
            ['name' => 'Apple', 'email' => 'p@p.pp', 'password' => 'heslo123'],
        ];
    }
}