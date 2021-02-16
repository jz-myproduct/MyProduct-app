<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Services\SlugService;
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

    public function load(ObjectManager $manager)
    {
        $company = new Company();

        $company->setPassword(
            $this->passwordEncoder->encodePassword($company, 'heslo123')
        );
        $company->setEmail('h@h.hh');
        $company->setName('Honzova firma');
        $company->setSlug(
            $this->slugService->createCompanySlug('Honzova firma')
        );

        $currentDateTime = new \DateTime();
        $company->setCreatedAt($currentDateTime);
        $company->setUpdatedAt($currentDateTime);

        $manager->persist($company);
        $manager->flush();
    }



}
