<?php


namespace App\Handler\Insight;


use App\Entity\Insight;
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

    public function handle(Insight $insight)
    {
        $this->manager->flush();

        // TODO update skóre
    }

}