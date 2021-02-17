<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Feedback;
use App\Services\SlugService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Fixtures extends Fixture
{
    private $passwordEncoder;
    private $slugService;
    /**
     * @var ObjectManager
     */
    private $manager;
    private $company;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, SlugService $slugService)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->slugService = $slugService;
    }

    /* TODO ROZDĚLIT PAK FIXTURES DO SAMOTNÉHO SOUBORU */
    public function load(ObjectManager $manager)
    {
        $currentDateTime = new \DateTime();

        /* Company */
        $company = new Company();
        $company->setPassword(
            $this->passwordEncoder->encodePassword($company, 'heslo123')
        );
        $company->setEmail('h@h.hh');
        $company->setName('Honzova firma');
        $company->setSlug(
            $this->slugService->createCompanySlug('Honzova firma')
        );
        $company->setCreatedAt($currentDateTime);
        $company->setUpdatedAt($currentDateTime);
        $company->setRoles( $company->getRoles() );

        $manager->persist($company);

        /* Feedback */
        foreach ($this->getFeedbackData() as $feedbackData)
        {
            $feedback = new Feedback();
            $feedback->setCompany( $company );
            $feedback->setDescription( $feedbackData[0] );
            $feedback->setSource( $feedbackData[1] );

            $currentDateTime = new \DateTime();
            $feedback->setCreatedAt( $currentDateTime );
            $feedback->setUpdatedAt( $currentDateTime );

            $manager->persist($feedback);
        }

        /* Persist */
        $manager->flush();
    }

    private function getFeedbackData()
    {
        return [
            ['feedback1', 'respondent 1'],
            ['feedback2', 'respondent 2'],
            ['feedback3', 'respondent 3'],
            ['feedback4', 'respondent 4']
        ];
    }
}
