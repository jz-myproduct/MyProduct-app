<?php


namespace App\View\BackOffice\Feature;



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

    public function createTag()
    {
        return $this->prepareTagChoices();
    }

    public function createState()
    {
        return $this->prepareStateChoices();
    }

    private function prepareStateChoices()
    {

        // add default value
        $array['Všechny'] = 0;

        foreach ($this->manager->getRepository(FeatureState::class)->findAll() as $state)
        {
            $array[$state->getName()] = $state->getId();
        }

        return $array;
    }

    private function prepareTagChoices()
    {

        $array = array();

        foreach($this->manager->getRepository(FeatureTag::class)->findAll() as $tag)
        {
            $array[$tag->getName()] = $tag->getId();
        }

        return $array;
    }
}