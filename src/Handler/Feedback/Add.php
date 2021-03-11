<?php


namespace App\Handler\Feedback;

use App\Entity\Company;
use App\Entity\Feedback;
use App\Events\FeedbackUpdatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;


class Add
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

    public function handle(Feedback $feedback, Company $company)
    {
        $feedback->setCompany($company);
        $feedback->setIsNew(true);

        $currentDateTime = new \DateTime();
        $feedback->setCreatedAt($currentDateTime);
        $feedback->setUpdatedAt($currentDateTime);
        $feedback->setFromPortal(false);

        $this->manager->persist($feedback);
        $this->manager->flush();

        return $feedback;
    }

}