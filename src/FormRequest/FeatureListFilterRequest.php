<?php


namespace App\FormRequest;

use Symfony\Component\Validator\Constraints as Constraints;
use App\Constraints as Custom;


class FeatureListFilterRequest
{

    /**
     * @Constraints\NotBlank
     * @var string
     */
    public $state;

    public $tags;

}