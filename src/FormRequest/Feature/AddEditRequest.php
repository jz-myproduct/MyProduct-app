<?php


namespace App\FormRequest\Feature;

use App\Entity\Feature;
use Symfony\Component\Validator\Constraints as Assert;



class AddEditRequest
{

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

    public $description;

    /**
     * @Assert\NotBlank()
     */
    public $state;

    public $tags;

    public static function fromFeature(Feature $feature)
    {
        $featureRequest = new self();
        $featureRequest->name = $feature->getName();
        $featureRequest->description = $feature->getDescription();
        $featureRequest->state = $feature->getState();
        $featureRequest->tags = $feature->getTags();

        return $featureRequest;
    }

}