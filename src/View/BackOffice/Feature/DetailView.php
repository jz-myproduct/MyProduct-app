<?php


namespace App\View\BackOffice\Feature;


use App\Entity\Feature;
use App\Entity\Insight;
use Doctrine\ORM\EntityManagerInterface;

class DetailView
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(Feature $feature)
    {
        $insightsCount = $this->manager->getRepository(Insight::class)
            ->getInsightsCountForFeature($feature);

        return [
            'feature' => $feature,
            'insightsCount' => $insightsCount
        ];
    }

}