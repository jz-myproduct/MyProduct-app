<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\FeatureState;
use App\Entity\FeatureTag;
use App\Entity\Feedback;
use App\Entity\Portal;
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

        /* Portal */
        $portal = new Portal();
        $portal->setDisplay(false);
        $portal->setName('Honzova firma');
        $portal->setSlug('Honzova firma');
        $portal->setCompany($company);
        $portal->setCreatedAt($currentDateTime);
        $portal->setUpdatedAt($currentDateTime);

        $manager->persist($portal);

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
        $manager->flush();

        /* Feedback, Feature */
        foreach ($this->getFeedbackFeatureData() as $data)
        {
            $tag = new FeatureTag();
            $tag->setName($data[5]);
            $tag->setSlug(
                $this->slugService->createGeneralSlug($data[5])
            );
            $tag->setCompany($company);

            $manager->persist($tag);

            $feature = new Feature();
            $feature->addTag($tag);
            $feature->setName( $data[3] );
            $feature->setDescription( $data[4]);
            $feature->setCompany( $company );
            $feature->setCreatedAt( $currentDateTime );
            $feature->setUpdatedAt( $currentDateTime );
            $feature->setScore(1);

            $feature->setstate(
                $manager->getRepository(FeatureState::class)->findInitialState()
            );

            $manager->persist($feature);

            $feedback = new Feedback();
            $feedback->setCompany( $company );
            $feedback->setDescription( $data[0] );
            $feedback->setSource( $data[1] );
            if($data[2] === 'active')
            {
                $feedback->setActiveStatus();
            }
            if($data[2] === 'new')
            {
                $feedback->setNewStatus();
            }

            $currentDateTime = new \DateTime();
            $feedback->setCreatedAt( $currentDateTime );
            $feedback->setUpdatedAt( $currentDateTime );
            $feedback->addFeature( $feature );

            $manager->persist($feedback);
        }

        /* Persist */
        $manager->flush();
    }


    private function getFeedbackFeatureData()
    {
        return [
            ['feedback1', 'respondent 1', 'new', 'feature1', 'feature popis 1', 'Tag1'],
            ['feedback2', 'respondent 2', 'new', 'feature2', 'feature popis 2', 'Tag2'],
            ['feedback3', 'respondent 3', 'active', 'feature3', 'feature popis 3', 'Tag3'],
            ['feedback4', 'respondent 4', 'active', 'feature4', 'feature popis 4', 'Tag4']
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
