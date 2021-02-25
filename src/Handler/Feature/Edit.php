<?php


namespace App\Handler\Feature;


use App\Entity\Feature;
use Doctrine\ORM\EntityManagerInterface;

class Edit
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
        $feature->setUpdatedAt(new \DateTime());

        $this->manager->flush();
    }

}