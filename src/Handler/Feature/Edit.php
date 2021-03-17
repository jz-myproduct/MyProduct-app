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

        foreach ($request->tags as $tag)
        {
            $feature->addTag($tag);
        }

        $this->manager->flush();
    }

}