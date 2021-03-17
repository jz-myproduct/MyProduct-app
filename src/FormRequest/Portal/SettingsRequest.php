<?php


namespace App\FormRequest\Portal;

use App\Entity\Portal;
use Symfony\Component\Validator\Constraints as Assert;


class SettingsRequest
{

    /**
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     */
    public $name;

    public $display;

    public static function fromPortal(Portal $portal)
    {
        $portalRequest = new self();
        $portalRequest->name = $portal->getName();
        $portalRequest->display = $portal->getDisplay();

        return $portalRequest;
    }

}