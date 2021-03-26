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

    public static $scrollTo = 'listScroll';

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(
        Company $company,
        FormView $form,
        FeatureState $state = null,
        $tagsParam = null,
        String $fulltext = null)
    {


        $tags = $this->manager->getRepository(FeatureTag::class)
            ->findBy( ['id' => $tagsParam, 'company' => $company ] );

        $featureList = $this->manager->getRepository(Feature::class)
            ->findCompanyFeaturesByTagAndState($tags, $company, $state, $fulltext);

        return [
            'featureList' => $featureList,
            'form' => $form,
            'tagsExist' => $company->getFeatureTags()->toArray() ? true : false,
            'isFiltered' => is_null($state) && is_null($tagsParam) && is_null($fulltext) ? false : true,
            'scrollTo' => self::$scrollTo
        ];
    }

}