<?php


namespace App\Handler\Feedback;


use App\Entity\Feedback;
use App\Events\FeedbackUpdatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Edit
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
        $feedback->setUpdatedAt(new \DateTime());

        $this->manager->flush();

        $this->dispatcher->dispatch(
            new FeedbackUpdatedEvent(),
            'feedback.updated.event');
    }

}