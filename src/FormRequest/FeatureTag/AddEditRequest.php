<?php


namespace App\FormRequest\FeatureTag;

use App\Entity\FeatureTag;
use Symfony\Component\Validator\Constraints as Assert;
use App\Constraint as Custom;

class AddEditRequest
{

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Custom\FeatureTagNameUnique()
     */
    public $name;

    public static function fromFeatureTag(FeatureTag $featureTag)
    {
        $featureTagRequest = new self();
        $featureTagRequest->name = $featureTag->getName();

        return $featureTagRequest;
    }

}