<?php


namespace App\FormRequest\Feature;

use Symfony\Component\Validator\Constraints as Constraints;
use App\Constraints as Custom;


class ListFilterRequest
{

    public $fulltext;

    /**
     * @Constraints\NotBlank
     * @var string
     */
    public $state;

    public $tags;

}