<?php


namespace App\Service;


use App\Entity\Company;
use App\Entity\Insight;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PortalFeatureUtils
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function recalculateFeedbackCountForPortalFeature(Company $company)
    {

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

}