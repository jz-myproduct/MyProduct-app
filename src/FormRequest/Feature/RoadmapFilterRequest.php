<?php


namespace App\FormRequest\Feature;

use Symfony\Component\Validator\Constraints as Constraints;



class RoadmapFilterRequest
{

    public $fulltext;

    public $tags;

}