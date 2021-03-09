<?php


namespace App\Handler\Insight;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;

class AddOnFeatureDetail
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(Feature $feature, Feedback $feedback, Company $company)
    {
        

    }


}