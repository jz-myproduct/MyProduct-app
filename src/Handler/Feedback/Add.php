<?php


namespace App\Handler\Feedback;


use App\Entity\Company;
use App\Entity\Feedback;
use App\FormRequest\Feedback\AddEditRequest;
use Doctrine\ORM\EntityManagerInterface;

class Add
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager= $manager;
    }

    /**
     * Add feedback from feedback section.
     *
     * @param AddEditRequest $request
     * @param Company $company
     * @return Feedback
     */
    public function add(AddEditRequest $request, Company $company)
    {
        $feedback = $this->prepareFeedback($request, $company, false);

        $this->manager->flush();

        return $feedback;
    }

    /**
     * Add feedback from portal.
     *
     * @param AddEditRequest $request
     * @param Company $company
     * @return Feedback
     */
    public function addFromPortal(AddEditRequest $request, Company $company)
    {
        $feedback = $this->prepareFeedback($request, $company, true);

        $this->manager->flush();

        return $feedback;
    }

    /**
     * Prepare feedback for adding insight. No manager flush, just return persisted object.
     *
     * @param AddEditRequest $request
     * @param Company $company
     * @return Feedback
     */
    public function addInsight(AddEditRequest $request, Company $company)
    {
        return $this->prepareFeedback($request, $company, false);
    }

    /**
     * Prepare feedback for adding insight. No manager flush, just return persisted object.
     *
     * @param AddEditRequest $request
     * @param Company $company
     * @return Feedback
     */
    public function addInsightFromPortal(AddEditRequest $request, Company $company)
    {
        return $this->prepareFeedback($request, $company, true);
    }


    private function prepareFeedback(AddEditRequest $request, Company $company, bool $isFromPortal)
    {
        $feedback = new Feedback();

        $feedback->setDescription($request->description);
        $feedback->setSource($request->source);

        $feedback->setCompany($company);
        $feedback->setIsNew(true);

        $currentDateTime = new \DateTime();
        $feedback->setCreatedAt($currentDateTime);
        $feedback->setUpdatedAt($currentDateTime);

        $feedback->setFromPortal($isFromPortal);

        $this->manager->persist($feedback);

        return $feedback;
    }

}