<?php


namespace App\FormRequest\PortalFeature;


use App\Entity\PortalFeature;
use Symfony\Component\Validator\Constraints as Assert;


class AddEditRequest
{

    /**
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     */
    public $name;

    public $description;

    /**
     * @Assert\NotBlank()
     */
    public $state;

    public $display;

    public $image;

    public static function fromPortalFeature(PortalFeature $portalFeature)
    {

        $portalRequest = new self();
        $portalRequest->name = $portalFeature->getName();
        $portalRequest->description = $portalFeature->getDescription();
        $portalRequest->state = $portalFeature->getState();
        $portalRequest->display = $portalFeature->getDisplay();
        $portalRequest->image = $portalFeature->getImage();

        return $portalRequest;
    }

}