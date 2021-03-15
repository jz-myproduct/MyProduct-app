<?php


namespace App\View\BackOffice\Feature;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\FeatureState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormView;

class ListView
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(Company $company, FormView $form, FeatureState $featureState = null)
    {

        if($featureState) {
            $featureList = $this->manager->getRepository(Feature::class)
                ->findBy(['company' => $company, 'state' => $featureState], ['score' => 'DESC']);
        } else {
            $featureList = $this->manager->getRepository(Feature::class)
                ->findBy(['company' => $company], ['score' => 'DESC']);
        }

        return [
            'featureList' => $featureList,
            'form' => $form
        ];
    }

}