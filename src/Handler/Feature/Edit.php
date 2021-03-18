<?php


namespace App\Handler\Feature;


use App\Entity\Feature;
use App\FormRequest\Feature\AddEditRequest;
use Doctrine\ORM\EntityManagerInterface;

class Edit
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(AddEditRequest $request, Feature $feature)
    {
        $feature->setUpdatedAt(new \DateTime());
        $feature->setName($request->name);
        $feature->setDescription($request->description);
        $feature->setState($request->state);

        if($feature->getTags()){
            foreach ($feature->getTags() as $tag)
            {
                $feature->removeTag($tag);
            }
        }

        if($request->tags){
            foreach ($request->tags as $tag)
            {
                $feature->addTag($tag);
            }
        }

        $this->manager->flush();
    }

}