<?php


namespace App\View\BackOffice\Feedback;


use App\Entity\Company;
use App\Entity\Feedback;
use App\Handler\Feedback\SwitchStatusRedirect;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormView;

class ListView
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public static $scrollTo = 'listScroll';

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
            'form' => $form,
            'isFiltered' => is_null($isNew) && is_null($fulltext) ? false : true,
            'scrollTo' => self::$scrollTo,
            'isNew' => $isNew,
            'fulltext' => $fulltext,
            'redirectTo' => SwitchStatusRedirect::$list
        ];
    }

}