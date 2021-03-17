<?php


namespace App\Service;


use App\Entity\Company;
use App\Entity\Insight;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class FeedbackCountService
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function recalculateFeedbackCountForPortalFeature()
    {
        $company = $this->entityManager->getRepository(Company::class)->getCompanyByEmail(
            $this->security->getUser()->getUsername());


        if($company)
        {

            /** @var Company $company */
            foreach($company->getFeatures() as $feature)
            {
                if($feature->getPortalFeature())
                {
                    $feedbackCount = $this->entityManager->getRepository(Insight::class)
                        ->getFeedbackCountForPortalFeature($feature);

                    $feature->getPortalFeature()->setFeedbackCount($feedbackCount);
                }
            }

            $this->entityManager->flush();

        }

        // TODO maybe throw exception here?

    }

}