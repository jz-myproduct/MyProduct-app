<?php


namespace App\FormRequest\Feature;

use Symfony\Component\Validator\Constraints as Constraints;



class RoadmapFilterRequest
{

    public $fulltext;

    public $tags;

    public static function fromArray(array $array)
    {
        $request = new self();
        $request->fulltext = $array['fulltext'];
        $request->tags = $array['tags'];

        return $request;
    }

}