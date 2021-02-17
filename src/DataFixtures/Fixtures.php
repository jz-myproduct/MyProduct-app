<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\FeatureState;
use App\Entity\Feedback;
use App\Services\SlugService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Fixtures extends Fixture
{
    private $passwordEncoder;
    private $slugService;

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
            if($feedbackData[2] === 'active')
            {
                $feedback->setActiveStatus();
            }
            if($feedbackData[2] === 'new')
            {
                $feedback->setNewStatus();
            }

            $currentDateTime = new \DateTime();
            $feedback->setCreatedAt( $currentDateTime );
            $feedback->setUpdatedAt( $currentDateTime );

            $manager->persist($feedback);
        }

        /* Features States */
        foreach ( $this->getFeatureStatesData() as $stateData)
        {
            $featureState = new FeatureState();
            $featureState->setName( $stateData[0] );
            $featureState->setSlug( $this->slugService->createGeneralSlug($stateData[0]) );
            $featureState->setPosition($stateData[1] );

            $manager->persist($featureState);
        }

        /* TODO vylepšit, abych nemusel 2x dělat flush */
        /* Persist */
        $manager->flush();

        /* Features */
        foreach ( $this->getFeatureData() as $featureData)
        {
            $feature = new Feature();
            $feature->setName( $featureData[0] );
            $feature->setDescription( $featureData[1]);
            $feature->setCompany( $company );
            $feature->setCreatedAt( $currentDateTime );
            $feature->setUpdatedAt( $currentDateTime );

            $feature->setstate(
                $manager->getRepository(FeatureState::class)->findInitialState()
            );

            $manager->persist($feature);
        }

        /* Persist */
        $manager->flush();
    }

    private function getFeedbackData()
    {
        return [
            ['feedback1', 'respondent 1', 'new'],
            ['feedback2', 'respondent 2', 'new'],
            ['feedback3', 'respondent 3', 'active'],
            ['feedback4', 'respondent 4', 'active']
        ];
    }

    private function getFeatureData()
    {
        return [
            ['feature1', 'feature popis 1'],
            ['feature2', 'feature popis 2'],
            ['feature3', 'feature popis 3'],
            ['feature4', 'feature popis 4'],
        ];
    }

    private function getFeatureStatesData()
    {
        return [
          ['Idea', 1],
          ['Upcoming', 2],
          ['In-progress', 3],
          ['Done', 4]
        ];
    }
}
