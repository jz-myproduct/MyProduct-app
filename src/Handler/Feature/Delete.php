<?php


namespace App\Handler\Feature;


use App\Entity\Feature;
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

    public function handle(Feature $feature)
    {
        $this->manager->remove($feature);
        $this->manager->flush();
    }

}