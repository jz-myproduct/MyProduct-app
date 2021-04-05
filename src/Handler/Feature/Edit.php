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
        $feature =
            $this->handleTags(
                $feature,
                $request->tags->toArray()
            );

        $feature->setUpdatedAt(new \DateTime());
        $feature->setName($request->name);
        $feature->setDescription($request->description);
        $feature->setState($request->state);

        $this->manager->flush();
    }

    private function handleTags(Feature $feature, $requestedTags)
    {

        foreach ($feature->getCompany()->getFeatureTags() as $tag)
        {
            if(! in_array($tag, $requestedTags))
            {
                $feature->removeTag($tag);
                continue;
            }

            if(in_array($tag, $requestedTags))
            {
                $feature->addTag($tag);
                continue;
            }
        }

        return $feature;
    }

}