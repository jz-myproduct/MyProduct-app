<?php


namespace App\Handler\FeatureTag;


use App\Entity\FeatureTag;
use Doctrine\ORM\EntityManagerInterface;

class Delete
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(FeatureTag $featureTag)
    {
        $this->manager->remove($featureTag);
        $this->manager->flush();
    }

}