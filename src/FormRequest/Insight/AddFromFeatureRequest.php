<?php


namespace App\FormRequest\Insight;

use Symfony\Component\Validator\Constraints as Assert;



class AddFromFeatureRequest
{

    /**
     * @Assert\NotBlank()
     */
    public $description;

    public $source;

    /**
     * @Assert\NotBlank()
     */
    public $weight;

}