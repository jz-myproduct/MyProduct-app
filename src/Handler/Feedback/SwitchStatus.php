<?php


namespace App\Handler\Feedback;


use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SwitchStatus
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var EntityManagerInterface
     */
    private $manager;


    public function __construct(EntityManagerInterface $manager, EventDispatcherInterface $dispatcher)
    {
        $this->manager = $manager;
        $this->dispatcher = $dispatcher;
    }

    public function handle(Feedback $feedback)
    {
        $feedback->switchIsNew();
        $feedback->setUpdatedAt(new \DateTime());

        $this->manager->flush();
    }

}