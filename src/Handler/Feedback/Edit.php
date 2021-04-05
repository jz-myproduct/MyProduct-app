<?php


namespace App\Handler\Feedback;


use App\Entity\Feedback;
use App\Event\FeedbackUpdatedEvent;
use App\FormRequest\Feedback\AddEditRequest;
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

    public function handle(Feedback $feedback, AddEditRequest $formRequest)
    {
        $feedback->setUpdatedAt(new \DateTime());
        $feedback->setDescription($formRequest->description);
        $feedback->setSource($formRequest->source);

        $this->manager->flush();

        $this->dispatcher->dispatch(
            new FeedbackUpdatedEvent(),
            'feedback.updated.event');
    }

}