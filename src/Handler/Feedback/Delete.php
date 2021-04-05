<?php


namespace App\Handler\Feedback;


use App\Entity\Feedback;
use App\Event\FeedbackUpdatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Delete
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

    public function delete(Feedback $feedback)
    {
        $this->manager->remove($feedback);
        $this->manager->flush();

        $this->dispatcher->dispatch(
            new FeedbackUpdatedEvent(),
            'feedback.updated.event');
    }

}