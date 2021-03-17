<?php


namespace App\Handler\Feedback;


use App\Entity\Company;
use App\Entity\Feedback;
use App\Form\Feedback\AddEditType;
use App\FormRequest\Feedback\AddEditRequest;
use App\FormRequest\Insight\AddFromFeatureRequest;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class AddFromPortal
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(AddFromFeatureRequest $request, Company $company)
    {
        $feedback = new Feedback();

        $feedback->setDescription($request->description);
        $feedback->setSource($request->source);

        $feedback->setCompany($company);
        $feedback->setIsNew(true);
        $feedback->setFromPortal(true);

        $currentDateTime = new \DateTime();
        $feedback->setCreatedAt($currentDateTime);
        $feedback->setUpdatedAt($currentDateTime);

        $this->manager->persist($feedback);

        return $feedback;
    }

    public function handleGeneral(AddEditRequest $request, Company $company)
    {
        $feedback = new Feedback();

        $feedback->setDescription($request->description);
        $feedback->setSource($request->source);

        $feedback->setCompany($company);
        $feedback->setIsNew(true);
        $feedback->setFromPortal(true);

        $currentDateTime = new \DateTime();
        $feedback->setCreatedAt($currentDateTime);
        $feedback->setUpdatedAt($currentDateTime);

        $this->manager->persist($feedback);
        $this->manager->flush();

        return $feedback;

    }

}