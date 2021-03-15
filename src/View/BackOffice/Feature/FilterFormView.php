<?php


namespace App\View\BackOffice\Feature;



use App\Entity\FeatureState;
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

    public function create()
    {
        return $this->prepareChoices();
    }

    private function prepareChoices()
    {

        // add default value
        $array['Všechny'] = 0;

        foreach ($this->manager->getRepository(FeatureState::class)->findAll() as $state)
        {
            $array[$state->getName()] = $state->getId();
        }

        return $array;

    }
}