<?php


namespace App\Handler\Feature;


use App\Entity\Feature;
use App\Entity\FeatureState;
use App\View\BackOffice\Feature\RoadmapView;
use Doctrine\ORM\EntityManagerInterface;


class MoveState
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;


    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(Feature $feature, $direction)
    {

        $newState = $this->manager->getRepository(FeatureState::class)
            ->findOneBy([
                'position' => $feature->getState()->getPosition() + $this->movePositionsBy($direction)
            ]);

        if($newState){

            $feature->setUpdatedAt(new \DateTime());
            $feature->setState($newState);

            $this->manager->flush();
        }

    }

    private function movePositionsBy($direction)
    {

        if($direction === RoadmapView::$previousDirection['slug'])
        {
            return RoadmapView::$previousDirection['int'];
        }

        if($direction === RoadmapView::$nextDirection['slug'])
        {
            return RoadmapView::$nextDirection['int'];
        }

        return 0;
    }


}