<?php


namespace App\Handler\Feature;


use App\Entity\Company;
use App\Entity\Feature;
use App\FormRequest\Feature\AddEditRequest;
use Doctrine\ORM\EntityManagerInterface;

class Add
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(AddEditRequest $request, Company $company)
    {
        $feature = new Feature();
        $feature->setName($request->name);
        $feature->setDescription($request->description);
        $feature->setState($request->state);

        if($request->tags){
            foreach($request->tags as $tag)
            {
                $feature->addTag($tag);
            }
        }

        $feature->setCompany($company);
        $feature->setInitialScore();

        $currentDateTime = new \DateTime();
        $feature->setCreatedAt($currentDateTime);
        $feature->setUpdatedAt($currentDateTime);

        $this->manager->persist($feature);
        $this->manager->flush();

        return $feature;
    }

}