<?php


namespace App\Service;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Internal\DiffElem;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class FeatureUtils
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function recalculateScoreForFeatures(Company $company)
    {

        foreach($company->getFeatures() as $feature)
        {
            $feature->setScore(
                $this->entityManager->getRepository(Insight::class)
                    ->getScoreCountForFeature($feature)
            );
        }
        $this->entityManager->flush();

    }

}