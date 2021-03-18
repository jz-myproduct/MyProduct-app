<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\FeatureState;
use App\Entity\FeatureTag;
use App\Entity\Feedback;
use App\Entity\Insight;
use App\Entity\InsightWeight;
use App\Entity\Portal;
use App\Entity\PortalFeatureState;
use App\Service\SlugService;
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

        foreach ($this->getInsightValue() as $insight)
        {
            $insightWeight = new InsightWeight();

            $insightWeight->setName($insight[0]);
            $insightWeight->setNumber($insight[1]);

            $manager->persist($insightWeight);
        }

        /* Company */
        $company = new Company();
        $company->setPassword(
            $this->passwordEncoder->encodePassword($company, 'heslo123')
        );
        $company->setEmail('h@h.hh');
        $company->setName('Honzova firma');
        $company->setSlug(
            $this->slugService->createInitialCompanySlug('Honzova firma')
        );
        $company->setCreatedAt($currentDateTime);
        $company->setUpdatedAt($currentDateTime);
        $company->setRoles( [Company::getUserRole()] );

        $manager->persist($company);

        /* DetailView */
        $portal = new Portal();
        $portal->setDisplay(false);
        $portal->setName('Honzova firma');
        $portal->setSlug(
            $this->slugService->createInitialPortalSlug('Honzova firma')
        );
        $portal->setCompany($company);
        $portal->setCreatedAt($currentDateTime);
        $portal->setUpdatedAt($currentDateTime);

        $manager->persist($portal);

        /* Features States */
        foreach ( $this->getFeatureStatesData() as $stateData)
        {
            $featureState = new FeatureState();
            $featureState->setName( $stateData[0] );
            $featureState->setSlug( $this->slugService->createCommonSlug($stateData[0]) );
            $featureState->setPosition($stateData[1] );
            $featureState->setColor($stateData[2]);

            $manager->persist($featureState);
        }


        /* DetailView features states */

        foreach ( $this->getPortalFeaturesData() as $portalFeatureStateData)
        {
            $portalFeatureState = new PortalFeatureState();
            $portalFeatureState->setName($portalFeatureStateData[0]);
            $portalFeatureState->setSlug(
                $this->slugService->createCommonSlug($portalFeatureStateData[0])
            );
            $portalFeatureState->setPosition($portalFeatureStateData[1]);

            $manager->persist($portalFeatureState);
        }


        /* TODO vylepšit, abych nemusel 2x dělat flush */
        $manager->flush();

        /* feedback, Feature */
        foreach ($this->getFeedbackFeatureData() as $data)
        {
            $tag = new FeatureTag();
            $tag->setName($data[5]);
            $tag->setSlug(
                $this->slugService->createCommonSlug($data[5])
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

            $feature->setstate(
                $manager->getRepository(FeatureState::class)->findInitialState()
            );

            $manager->persist($feature);

            $feedback = new Feedback();
            $feedback->setCompany( $company );
            $feedback->setDescription( $data[0] );
            $feedback->setSource( $data[1] );
            $feedback->setFromPortal(false);
            if($data[2] === 'active')
            {
                $feedback->setIsNew(false);
            }
            if($data[2] === 'new')
            {
                $feedback->setIsNew(true);
            }

            $currentDateTime = new \DateTime();
            $feedback->setCreatedAt( $currentDateTime );
            $feedback->setUpdatedAt( $currentDateTime );

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
          ['Idea', 1, 'indianred'],
          ['Upcoming', 2, 'cornflowerblue'],
          ['In-progress', 3, 'darkorange'],
          ['Done', 4, 'seagreen']
        ];
    }

    private function getPortalFeaturesData()
    {
        return [
          ['Nápady', 1],
          ['Ve vývoji', 2],
          ['Hotovo', 3]
        ];
    }

    private function getInsightValue()
    {
        return [
          ['Not important', 1],
          ['Nice-to-have', 2],
          ['Important', 3],
          ['Critical', 4]
        ];
    }

}
