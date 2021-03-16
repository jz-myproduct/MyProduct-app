<?php


namespace App\Handler\Insight;


use App\Entity\Insight;
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

    public function handle(Insight $insight)
    {
        $insight->getFeature()->setScoreDownBy(
            $insight->getWeight()->getNumber()
        );

        $this->manager->remove($insight);
        $this->manager->flush();
    }

}