<?php


namespace App\FormRequest\Feature;

use Symfony\Component\Validator\Constraints as Constraints;
use App\Constraint as Custom;


class ListFilterRequest
{

    public $fulltext;

    public $state;

    public $tags;

    public static function fromArray(array $array)
    {
        $request = new self();
        $request->state = $array['state'];
        $request->tags = $array['tags'];
        $request->fulltext = $array['fulltext'];

        return $request;
    }

}