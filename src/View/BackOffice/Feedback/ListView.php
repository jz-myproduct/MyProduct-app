<?php


namespace App\View\BackOffice\Feedback;


use App\Entity\Company;
use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;

class ListView
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function create(Company $company)
    {
        $feedbackList = $this->manager->getRepository(Feedback::class)
            ->findBy(['company' => $company], ['isNew' => 'DESC']);

        return [
            'feedbackList' => $feedbackList
        ];
    }

}