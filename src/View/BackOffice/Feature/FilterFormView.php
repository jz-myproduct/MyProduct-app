<?php


namespace App\View\BackOffice\Feature;



use App\Entity\Company;
use App\Entity\FeatureState;
use App\Entity\FeatureTag;
use Doctrine\ORM\EntityManagerInterface;

class FilterFormView
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function createTags(Company $company)
    {
        return $this->prepareTagChoices($company);
    }

    public function createStates()
    {
        return $this->prepareStateChoices();
    }

    private function prepareStateChoices()
    {

        // add default value
        $array['All'] = 0;

        foreach ($this->manager->getRepository(FeatureState::class)->findAll() as $state)
        {
            $array[$state->getName()] = $state->getId();
        }

        return $array;
    }

    private function prepareTagChoices(Company $company)
    {

        $array = array();

        foreach($this->manager->getRepository(FeatureTag::class)->findBy(['company' => $company]) as $tag)
        {
            $array[$tag->getName()] = $tag->getId();
        }

        return $array;
    }
}