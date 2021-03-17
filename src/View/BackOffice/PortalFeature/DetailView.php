<?php


namespace App\View\BackOffice\PortalFeature;


use App\Entity\Feature;
use App\Entity\Insight;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormView;

class DetailView
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(FormView $form, Feature $feature)
    {
        $insightsCount = $this->manager->getRepository(Insight::class)
            ->getInsightsCountForFeature($feature);

        return [
            'form' => $form,
            'feature' => $feature,
            'portalFeature' => $feature->getPortalFeature() ?? null,
            'insightsCount' => $insightsCount
        ];
    }
}