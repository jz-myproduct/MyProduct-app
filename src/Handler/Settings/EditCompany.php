<?php


namespace App\Handler\Settings;


use App\Entity\Company;
use App\FormRequest\Settings\InfoRequest;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;

class EditCompany
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var SlugService
     */
    private $slugService;


    public function __construct(EntityManagerInterface $manager, SlugService $slugService)
    {
        $this->slugService = $slugService;
        $this->manager = $manager;
    }

    public function handle(InfoRequest $request, Company $company)
    {
        if($company->getName() !== $request->name)
        {
            $company->setName($request->name);
            $company->setSlug(
                $this->slugService->createCompanySlug($request->name, $company)
            );
        }

        $company->setEmail($request->username);

        $company->setUpdatedAt(new \DateTime());

        $this->manager->flush();

        return $company;
    }

}