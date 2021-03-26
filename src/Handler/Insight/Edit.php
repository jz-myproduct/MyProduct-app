<?php


namespace App\Handler\Insight;


use App\Entity\Insight;
use App\Events\FeedbackUpdatedEvent;
use App\FormRequest\Insight\AddFromFeedbackRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Edit
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EntityManagerInterface $manager, EventDispatcherInterface $dispatcher)
    {
        $this->manager = $manager;
        $this->dispatcher = $dispatcher;
    }

    public function handle(AddFromFeedbackRequest $request, Insight $insight)
    {
        $insight->setWeight($request->weight);

        $insight->getFeedback()->setUpdatedAt(new \DateTime());
        $insight->getFeature()->setUpdatedAt(new \DateTime());

        $this->manager->flush();

        $this->dispatcher->dispatch(
            new FeedbackUpdatedEvent(),
            'feedback.updated.event');
    }

}