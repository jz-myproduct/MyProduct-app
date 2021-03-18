<?php


namespace App\View\BackOffice\Feature;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\FeatureState;
use App\Entity\FeatureTag;
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

    public function create(Company $company, FormView $form, FeatureState $state = null, $tagsParam = [])
    {

        $tags = $this->manager->getRepository(FeatureTag::class)
            ->findBy( ['id' => $tagsParam ] );

        $featureList = $this->manager->getRepository(Feature::class)
            ->findCompanyFeaturesByTag($tags, $company, $state);


        return [
            'featureList' => $featureList,
            'form' => $form,
            'tagsExist' => $company->getFeatureTags()->toArray() ? true : false
        ];
    }

}