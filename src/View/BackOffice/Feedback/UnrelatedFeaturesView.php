<?php


namespace App\View\BackOffice\Feedback;


use App\Entity\Company;
use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;

class UnrelatedFeaturesView
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(Company $company, Feedback $feedback)
    {
        return $this->manager->getRepository(Feedback::class)
            ->getUnUsedFeaturesForFeedback($feedback, $company);
    }

}