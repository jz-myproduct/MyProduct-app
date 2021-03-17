<?php


namespace App\Services;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Internal\DiffElem;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class FeatureScoreService
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

    public function recalculateScoreForFeatures()
    {
        $company = $this->entityManager->getRepository(Company::class)->getCompanyByEmail(
                     $this->security->getUser()->getUsername());

        if($company){

            /** @var Company $company */
            foreach($company->getFeatures() as $feature)
            {
                $feature->setScore(
                    $this->entityManager->getRepository(Insight::class)
                        ->getScoreCountForFeature($feature)
                );
            }
            $this->entityManager->flush();

        }

        // TODO maybe throw exception here?

    }

}