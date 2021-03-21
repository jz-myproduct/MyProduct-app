<?php


namespace App\View\BackOffice\Feedback;


use App\Entity\Company;
use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormView;

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

    public function create(Company $company, FormView $form, $isNew, $fulltext)
    {
        $feedbackList = $this->manager->getRepository(Feedback::class)
            ->findForFilteredList($company, $fulltext, $isNew);

        return [
            'feedbackList' => $feedbackList,
            'form' => $form
        ];
    }

}