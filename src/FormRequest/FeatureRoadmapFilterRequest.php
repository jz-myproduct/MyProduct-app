<?php


namespace App\FormRequest;

use Symfony\Component\Validator\Constraints as Constraints;



class FeatureRoadmapFilterRequest
{

    /**
     * @Constraints\NotBlank
     */
    public $tags;

}