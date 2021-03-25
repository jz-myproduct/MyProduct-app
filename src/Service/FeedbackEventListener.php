<?php


namespace App\Service;


use App\Entity\Company;
use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\Security\Core\Security;

class FeedbackEventListener
{
    /**
     * @var FeatureUtils
     */


    private $scoreService;
    /**
     * @var PortalFeatureUtils
     */
    private $countService;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var Security
     */
    private $security;

    public function __construct(
        FeatureUtils $scoreService,
        PortalFeatureUtils $countService,
        EntityManagerInterface $manager,
        Security $security)
    {
        $this->scoreService = $scoreService;
        $this->countService = $countService;
        $this->manager = $manager;
        $this->security = $security;
    }

    public function onFeedbackUpdatedEvent()
    {

        if($company = $this->getCompany()){

            $this->scoreService->recalculateScoreForFeatures($company);
            $this->countService->recalculateFeedbackCountForPortalFeature($company);

        }

    }

    private function getCompany()
    {
        return $this->manager->getRepository(Company::class)->findCompanyByEmail(
                    $this->security->getUser()->getUsername());
    }

}